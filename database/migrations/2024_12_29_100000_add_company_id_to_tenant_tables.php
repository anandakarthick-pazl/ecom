<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'categories',
            'products', 
            'customers',
            'orders',
            'order_items',
            'banners',
            'offers',
            'carts',
            'suppliers',
            'purchase_orders',
            'purchase_order_items',
            'estimates',
            'estimate_items',
            'goods_receipt_notes',
            'grn_items',
            'stock_adjustments',
            'stock_adjustment_items',
            'pos_sales',
            'pos_sale_items',
            'app_settings',
            'notifications'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'company_id')) {
                        $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
                        $table->index('company_id');
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'categories',
            'products', 
            'customers',
            'orders',
            'order_items',
            'banners',
            'offers',
            'carts',
            'suppliers',
            'purchase_orders',
            'purchase_order_items',
            'estimates',
            'estimate_items',
            'goods_receipt_notes',
            'grn_items',
            'stock_adjustments',
            'stock_adjustment_items',
            'pos_sales',
            'pos_sale_items',
            'app_settings',
            'notifications'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'company_id')) {
                        $table->dropForeign(['company_id']);
                        $table->dropColumn('company_id');
                    }
                });
            }
        }
    }
};
