<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Offer;
use App\Models\Category;
use Illuminate\Http\Request;

class OfferPriorityTestController extends Controller
{
    /**
     * Test the offer priority system
     */
    public function testOfferPriority()
    {
        // Get some test products
        $products = Product::with('category')->take(5)->get();
        
        $testResults = [];
        
        foreach ($products as $product) {
            $offerDetails = $product->getOfferDetails();
            $activeOfferSource = $product->getActiveOfferSource();
            
            $testResults[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'original_price' => $product->price,
                'product_onboarding_discount' => $product->discount_price,
                'has_offers_page_offers' => $product->hasActiveOffersPageOffers(),
                'has_product_onboarding_discount' => $product->hasProductOnboardingDiscount(),
                'active_offer_source' => $activeOfferSource,
                'offer_details' => $offerDetails,
                'final_price' => $offerDetails ? $offerDetails['discounted_price'] : $product->price,
                'savings' => $offerDetails ? $offerDetails['savings'] : 0,
                'discount_percentage' => $offerDetails ? $offerDetails['discount_percentage'] : 0,
            ];
        }
        
        return view('test.offer-priority', compact('testResults'));
    }
    
    /**
     * Create test data for demonstration
     */
    public function createTestData()
    {
        // Create test offers for demonstration
        $product = Product::first();
        
        if (!$product) {
            return response()->json(['error' => 'No products found. Please create products first.']);
        }
        
        // Create an offers page offer for this product
        $offer = Offer::create([
            'name' => 'Summer Sale - Product Specific',
            'code' => 'SUMMER2024',
            'type' => 'product',
            'discount_type' => 'percentage',
            'value' => 25.00,
            'product_id' => $product->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
            'company_id' => $product->company_id,
        ]);
        
        // Also set a product onboarding discount
        $product->update([
            'discount_price' => $product->price * 0.85 // 15% discount
        ]);
        
        return response()->json([
            'message' => 'Test data created successfully',
            'offer_id' => $offer->id,
            'product_id' => $product->id,
            'offers_page_discount' => '25%',
            'product_onboarding_discount' => '15%',
            'expected_result' => 'Offers page discount (25%) should take priority over product onboarding discount (15%)'
        ]);
    }
    
    /**
     * Test priority scenarios
     */
    public function testPriorityScenarios()
    {
        $scenarios = [];
        
        // Scenario 1: Product with only offers page offer
        $product1 = Product::whereHas('offers', function($query) {
            $query->active()->current();
        })->first();
        
        if ($product1) {
            $scenarios['offers_page_only'] = [
                'product' => $product1->name,
                'price' => $product1->price,
                'offer_details' => $product1->getOfferDetails(),
                'source' => $product1->getActiveOfferSource(),
                'description' => 'Product with active offers page offer'
            ];
        }
        
        // Scenario 2: Product with only onboarding discount
        $product2 = Product::whereNotNull('discount_price')
            ->whereDoesntHave('offers', function($query) {
                $query->active()->current();
            })->first();
            
        if ($product2) {
            $scenarios['onboarding_only'] = [
                'product' => $product2->name,
                'price' => $product2->price,
                'offer_details' => $product2->getOfferDetails(),
                'source' => $product2->getActiveOfferSource(),
                'description' => 'Product with only onboarding discount'
            ];
        }
        
        // Scenario 3: Product with both (priority test)
        $product3 = Product::whereNotNull('discount_price')
            ->whereHas('offers', function($query) {
                $query->active()->current();
            })->first();
            
        if ($product3) {
            $scenarios['both_offers'] = [
                'product' => $product3->name,
                'price' => $product3->price,
                'offer_details' => $product3->getOfferDetails(),
                'source' => $product3->getActiveOfferSource(),
                'description' => 'Product with both offers (priority test)',
                'onboarding_discount' => $product3->discount_price,
                'offers_page_offers' => $product3->getApplicableOffers()->count()
            ];
        }
        
        return response()->json([
            'priority_system_test' => [
                'rule' => 'Offers page offers take priority over product onboarding discounts',
                'scenarios' => $scenarios
            ]
        ]);
    }
}
