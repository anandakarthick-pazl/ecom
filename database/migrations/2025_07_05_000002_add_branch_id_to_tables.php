<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add branch_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to orders table for branch-wise order tracking
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to products table for branch-specific inventory
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to customers table for branch assignment
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to pos_sales table
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to stock_adjustments table
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to estimates table
        Schema::table('estimates', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
        
        // Add branch_id to goods_receipt_notes table
        Schema::table('goods_receipt_notes', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
            $table->index(['company_id', 'branch_id']);
        });
    }

    public function down()
    {
        // Remove branch_id from all tables
        $tables = [
            'users', 'orders', 'products', 'customers', 'suppliers', 
            'purchase_orders', 'pos_sales', 'stock_adjustments', 
            'estimates', 'goods_receipt_notes'
        ];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropIndex(['company_id', 'branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
