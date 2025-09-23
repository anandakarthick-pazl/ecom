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
            // Add pricing fields if they don't exist
            if (!Schema::hasColumn('order_items', 'mrp_price')) {
                $table->decimal('mrp_price', 10, 2)->after('price')->nullable()->comment('Original MRP price before discounts');
            }
            
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->after('mrp_price')->default(0)->comment('Total discount amount applied');
            }
            
            if (!Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->after('discount_amount')->default(0)->comment('Discount percentage applied');
            }
            
            if (!Schema::hasColumn('order_items', 'offer_id')) {
                $table->unsignedBigInteger('offer_id')->after('discount_percentage')->nullable()->comment('Applied offer ID');
            }
            
            if (!Schema::hasColumn('order_items', 'offer_name')) {
                $table->string('offer_name')->after('offer_id')->nullable()->comment('Applied offer name');
            }
            
            if (!Schema::hasColumn('order_items', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->after('offer_name')->default(0)->comment('Tax percentage applied');
            }
            
            if (!Schema::hasColumn('order_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->after('tax_percentage')->default(0)->comment('Total tax amount');
            }
            
            // Add indexes for performance
            $table->index('offer_id');
            $table->index('mrp_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['offer_id']);
            $table->dropIndex(['mrp_price']);
            
            $table->dropColumn([
                'mrp_price',
                'discount_amount',
                'discount_percentage',
                'offer_id',
                'offer_name',
                'tax_percentage',
                'tax_amount'
            ]);
        });
    }
};
