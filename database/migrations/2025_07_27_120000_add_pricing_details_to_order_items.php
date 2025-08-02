<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add MRP (Maximum Retail Price) - the original product price
            if (!Schema::hasColumn('order_items', 'mrp_price')) {
                $table->decimal('mrp_price', 10, 2)->default(0)->after('price')->comment('MRP/Original price of the product');
            }
            
            // Add offer/discount details
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('mrp_price')->comment('Discount amount applied');
            }
            
            if (!Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount')->comment('Discount percentage applied');
            }
            
            if (!Schema::hasColumn('order_items', 'offer_id')) {
                $table->unsignedBigInteger('offer_id')->nullable()->after('discount_percentage')->comment('ID of the offer applied');
            }
            
            if (!Schema::hasColumn('order_items', 'offer_name')) {
                $table->string('offer_name')->nullable()->after('offer_id')->comment('Name of the offer applied');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'mrp_price',
                'discount_amount', 
                'discount_percentage',
                'offer_id',
                'offer_name'
            ]);
        });
    }
};
