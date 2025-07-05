<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add company_id and branch_id to purchase_orders table if not exists
        if (!Schema::hasColumn('purchase_orders', 'company_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('approved_by')->constrained()->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Add branch_id to purchase_orders table if not exists
        if (!Schema::hasColumn('purchase_orders', 'branch_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->onDelete('set null');
                $table->index(['company_id', 'branch_id']);
            });
        }

        // Add company_id to any other tables that might need it
        $tablesToCheck = [
            'estimates', 'goods_receipt_notes', 'stock_adjustments'
        ];

        foreach ($tablesToCheck as $tableName) {
            if (Schema::hasTable($tableName)) {
                // Add company_id if not exists
                if (!Schema::hasColumn($tableName, 'company_id')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                        $table->index('company_id');
                    });
                }
            }
        }
    }

    public function down()
    {
        // Remove branch_id from purchase_orders
        if (Schema::hasColumn('purchase_orders', 'branch_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropIndex(['company_id', 'branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        // Remove company_id from purchase_orders if it was added by this migration
        if (Schema::hasColumn('purchase_orders', 'company_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        $tablesToCheck = [
            'estimates', 'goods_receipt_notes', 'stock_adjustments'
        ];

        foreach ($tablesToCheck as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropIndex(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
