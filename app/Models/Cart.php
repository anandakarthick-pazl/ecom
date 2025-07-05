<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\BelongsToTenantEnhanced;

class Cart extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'session_id', 'product_id', 'quantity', 'price', 'company_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    public static function getCartItems($sessionId)
    {
        return self::with('product')
                  ->where('session_id', $sessionId)
                  ->get();
    }

    public static function addToCart($sessionId, $productId, $quantity = 1)
    {
        $product = Product::findOrFail($productId);
        
        if (!$product->isInStock($quantity)) {
            return false;
        }

        $cartItem = self::where('session_id', $sessionId)
                       ->where('product_id', $productId)
                       ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if (!$product->isInStock($newQuantity)) {
                return false;
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            self::create([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->final_price,
            ]);
        }

        return true;
    }

    public static function updateQuantity($sessionId, $productId, $quantity)
    {
        $cartItem = self::where('session_id', $sessionId)
                       ->where('product_id', $productId)
                       ->first();

        if (!$cartItem) {
            return false;
        }

        if ($quantity <= 0) {
            $cartItem->delete();
            return true;
        }

        if (!$cartItem->product->isInStock($quantity)) {
            return false;
        }

        $cartItem->update(['quantity' => $quantity]);
        return true;
    }

    public static function removeFromCart($sessionId, $productId)
    {
        return self::where('session_id', $sessionId)
                  ->where('product_id', $productId)
                  ->delete();
    }

    public static function clearCart($sessionId)
    {
        return self::where('session_id', $sessionId)->delete();
    }

    public static function getCartTotal($sessionId)
    {
        return self::where('session_id', $sessionId)->sum(DB::raw('quantity * price'));
    }

    public static function getCartCount($sessionId)
    {
        return self::where('session_id', $sessionId)->count(); // Count distinct products, not total quantity
    }
    
    public static function getCartTotalQuantity($sessionId)
    {
        return self::where('session_id', $sessionId)->sum('quantity'); // For total quantity if needed
    }
}
