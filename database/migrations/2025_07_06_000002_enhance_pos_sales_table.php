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
        Schema::table('pos_sales', function (Blueprint $table) {
            // Add company_id for multi-tenant support if missing
            if (!Schema::hasColumn('pos_sales', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }
            
            // Add additional payment methods if not already present
            $table->enum('payment_method', ['cash', 'card', 'upi', 'gpay', 'paytm', 'phonepe', 'mixed'])->default('cash')->change();
        });
        
        // Add indexes separately to avoid conflicts
        try {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->index('company_id', 'pos_sales_company_id_index');
            });
        } catch (\Exception $e) {
            // Index might already exist, continue
        }
        
        try {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->index('customer_phone', 'pos_sales_customer_phone_index');
            });
        } catch (\Exception $e) {
            // Index might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes first
        try {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->dropIndex('pos_sales_company_id_index');
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }
        
        try {
            Schema::table('pos_sales', function (Blueprint $table) {
                $table->dropIndex('pos_sales_customer_phone_index');
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }
        
        Schema::table('pos_sales', function (Blueprint $table) {
            // Reset payment method enum to original values
            $table->enum('payment_method', ['cash', 'card', 'upi', 'mixed'])->default('cash')->change();
            
            // Optionally drop company_id if added by this migration
            // Uncomment the line below if you want to remove company_id in rollback
            // $table->dropColumn('company_id');
        });
    }
};
