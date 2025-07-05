<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // This migration will be used to trigger seeding
        // The actual data creation will be handled by the seeder
        
        // Add any additional schema modifications if needed
        if (!Schema::hasColumn('products', 'company_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('categories', 'company_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('customers', 'company_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('orders', 'company_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('banners', 'company_id')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('offers', 'company_id')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('suppliers', 'company_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // Remove company_id columns
        $tables = ['products', 'categories', 'customers', 'orders', 'banners', 'offers', 'suppliers'];
        
        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $table_blueprint) {
                    $table_blueprint->dropForeign(['company_id']);
                    $table_blueprint->dropColumn('company_id');
                });
            }
        }
    }
};
