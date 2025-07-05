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
        // Add tax_percentage to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount_price')->comment('GST tax percentage');
            }
        });

        // Add tax columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('delivery_charge')->comment('Total tax amount');
            }
            if (!Schema::hasColumn('orders', 'cgst_amount')) {
                $table->decimal('cgst_amount', 10, 2)->default(0)->after('tax_amount')->comment('CGST amount');
            }
            if (!Schema::hasColumn('orders', 'sgst_amount')) {
                $table->decimal('sgst_amount', 10, 2)->default(0)->after('cgst_amount')->comment('SGST amount');
            }
        });

        // Add tax columns to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('quantity')->comment('Tax percentage at time of order');
            }
            if (!Schema::hasColumn('order_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percentage')->comment('Tax amount for this item');
            }
        });

        // Add tax columns to pos_sales table if it exists
        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_sales', 'tax_amount')) {
                    $table->decimal('tax_amount', 10, 2)->default(0)->after('discount')->comment('Total tax amount');
                }
                if (!Schema::hasColumn('pos_sales', 'cgst_amount')) {
                    $table->decimal('cgst_amount', 10, 2)->default(0)->after('tax_amount')->comment('CGST amount');
                }
                if (!Schema::hasColumn('pos_sales', 'sgst_amount')) {
                    $table->decimal('sgst_amount', 10, 2)->default(0)->after('cgst_amount')->comment('SGST amount');
                }
            });
        }

        // Add tax columns to pos_sale_items table if it exists
        if (Schema::hasTable('pos_sale_items')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_sale_items', 'tax_percentage')) {
                    $table->decimal('tax_percentage', 5, 2)->default(0)->after('quantity')->comment('Tax percentage');
                }
                if (!Schema::hasColumn('pos_sale_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percentage')->comment('Tax amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['tax_percentage']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'cgst_amount', 'sgst_amount']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['tax_percentage', 'tax_amount']);
        });

        if (Schema::hasTable('pos_sales')) {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->dropColumn(['tax_amount', 'cgst_amount', 'sgst_amount']);
            });
        }

        if (Schema::hasTable('pos_sale_items')) {
            Schema::table('pos_sale_items', function (Blueprint $table) {
                $table->dropColumn(['tax_percentage', 'tax_amount']);
            });
        }
    }
};
