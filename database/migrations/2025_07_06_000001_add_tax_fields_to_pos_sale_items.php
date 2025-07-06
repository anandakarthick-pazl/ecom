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
        Schema::table('pos_sale_items', function (Blueprint $table) {
            // Add tax-related fields to pos_sale_items table
            if (!Schema::hasColumn('pos_sale_items', 'tax_percentage')) {
                $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount_amount');
            }
            
            if (!Schema::hasColumn('pos_sale_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percentage');
            }
            
            // Add company_id for multi-tenant support if missing
            if (!Schema::hasColumn('pos_sale_items', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }
            
            // Add individual discount percentage for reporting
            if (!Schema::hasColumn('pos_sale_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $columns = ['tax_percentage', 'tax_amount', 'company_id', 'discount_percentage'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('pos_sale_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
