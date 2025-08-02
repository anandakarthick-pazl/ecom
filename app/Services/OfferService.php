<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;

class OfferService
{
    /**
     * Get all applicable offers for a product
     *
     * @param Product $product
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApplicableOffers(Product $product)
    {
        return Offer::where(function($query) use ($product) {
            // Product-specific offers
            $query->where('type', 'product')
                  ->where('product_id', $product->id);
        })->orWhere(function($query) use ($product) {
            // Category-specific offers
            $query->where('type', 'category')
                  ->where('category_id', $product->category_id);
        })->orWhere(function($query) {
            // General offers (percentage/fixed)
            $query->whereIn('type', ['percentage', 'fixed'])
                  ->whereNull('category_id')
                  ->whereNull('product_id');
        })->where('is_active', true)
          ->where('start_date', '<=', today())
          ->where('end_date', '>=', today())
          ->orderBy('value', 'desc') // Higher discount first
          ->get();
    }

    /**
     * Get the best applicable offer for a product
     *
     * @param Product $product
     * @return Offer|null
     */
    public function getBestOffer(Product $product)
    {
        $offers = $this->getApplicableOffers($product);
        
        if ($offers->isEmpty()) {
            return null;
        }

        $bestOffer = null;
        $bestDiscount = 0;

        foreach ($offers as $offer) {
            $discount = $this->calculateOfferDiscount($offer, $product);
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestOffer = $offer;
            }
        }

        return $bestOffer;
    }

    /**
     * Calculate discount amount for an offer on a product
     *
     * @param Offer $offer
     * @param Product $product
     * @param float $customAmount Optional custom amount to calculate discount on
     * @return float
     */
    public function calculateOfferDiscount(Offer $offer, Product $product, $customAmount = null)
    {
        if (!$offer->isValid()) {
            return 0;
        }

        $amount = $customAmount ?: $product->price;

        // Check minimum amount requirement
        if ($offer->minimum_amount && $amount < $offer->minimum_amount) {
            return 0;
        }

        // Check if offer applies to this product
        if ($offer->type === 'product' && $offer->product_id !== $product->id) {
            return 0;
        }

        // Check if offer applies to this category
        if ($offer->type === 'category' && $offer->category_id !== $product->category_id) {
            return 0;
        }

        // Calculate discount based on offer type and discount type
        if ($offer->type === 'percentage' || $offer->discount_type === 'percentage') {
            return min($amount * ($offer->value / 100), $amount);
        }

        // Fixed/flat amount discount
        return min($offer->value, $amount);
    }

    /**
     * Get effective price for a product after applying best offer
     *
     * @param Product $product
     * @return float
     */
    public function getEffectivePrice(Product $product)
    {
        // First check if manual discount_price is set
        if ($product->discount_price && $product->discount_price > 0) {
            return $product->discount_price;
        }

        // Check for applicable offers
        $bestOffer = $this->getBestOffer($product);
        if ($bestOffer) {
            $discount = $this->calculateOfferDiscount($bestOffer, $product);
            return max(0, $product->price - $discount);
        }

        return $product->price;
    }

    /**
     * Get offer details for display
     *
     * @param Product $product
     * @return array|null
     */
    public function getOfferDetails(Product $product)
    {
        $bestOffer = $this->getBestOffer($product);
        if (!$bestOffer) {
            return null;
        }

        $discount = $this->calculateOfferDiscount($bestOffer, $product);
        $discountedPrice = max(0, $product->price - $discount);

        return [
            'offer' => $bestOffer,
            'original_price' => $product->price,
            'discounted_price' => $discountedPrice,
            'discount_amount' => $discount,
            'discount_percentage' => round(($discount / $product->price) * 100),
            'savings' => $discount,
            'is_category_offer' => $bestOffer->type === 'category',
            'is_product_offer' => $bestOffer->type === 'product',
            'is_general_offer' => in_array($bestOffer->type, ['percentage', 'fixed'])
        ];
    }

    /**
     * Get all active offers for a category
     *
     * @param Category $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoryOffers(Category $category)
    {
        return Offer::where('type', 'category')
                   ->where('category_id', $category->id)
                   ->where('is_active', true)
                   ->where('start_date', '<=', today())
                   ->where('end_date', '>=', today())
                   ->orderBy('value', 'desc')
                   ->get();
    }

    /**
     * Apply offers to a collection of products
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function applyOffersToProducts($products)
    {
        foreach ($products as $product) {
            $product->effective_price = $this->getEffectivePrice($product);
            $product->offer_details = $this->getOfferDetails($product);
            $product->has_offer = $product->effective_price < $product->price;
            $product->discount_percentage = $product->has_offer 
                ? round((($product->price - $product->effective_price) / $product->price) * 100)
                : 0;
        }

        return $products;
    }

    /**
     * Check if a product has any active offers
     *
     * @param Product $product
     * @return bool
     */
    public function hasActiveOffers(Product $product)
    {
        return $this->getApplicableOffers($product)->count() > 0;
    }

    /**
     * Get discount percentage for a product
     *
     * @param Product $product
     * @return int
     */
    public function getDiscountPercentage(Product $product)
    {
        $effectivePrice = $this->getEffectivePrice($product);
        if ($effectivePrice < $product->price) {
            return round((($product->price - $effectivePrice) / $product->price) * 100);
        }
        return 0;
    }

    /**
     * Validate if an offer can be applied to a cart total
     *
     * @param Offer $offer
     * @param float $cartTotal
     * @param array $cartItems
     * @return bool
     */
    public function canApplyOfferToCart(Offer $offer, $cartTotal, $cartItems = [])
    {
        if (!$offer->isValid()) {
            return false;
        }

        // Check minimum amount
        if ($offer->minimum_amount && $cartTotal < $offer->minimum_amount) {
            return false;
        }

        // For category/product specific offers, check if cart contains applicable items
        if ($offer->type === 'category' || $offer->type === 'product') {
            foreach ($cartItems as $item) {
                if ($offer->type === 'category' && $item['product']->category_id === $offer->category_id) {
                    return true;
                }
                if ($offer->type === 'product' && $item['product']->id === $offer->product_id) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }
}
