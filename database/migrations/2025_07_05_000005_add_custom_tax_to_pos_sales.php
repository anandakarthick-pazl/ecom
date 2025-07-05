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
            // Add fields to support custom tax override
            $table->boolean('custom_tax_enabled')->default(false)->after('tax_amount');
            $table->decimal('custom_tax_amount', 12, 2)->default(0)->after('custom_tax_enabled');
            
            // Add CGST and SGST fields if they don't exist
            if (!Schema::hasColumn('pos_sales', 'cgst_amount')) {
                $table->decimal('cgst_amount', 12, 2)->default(0)->after('custom_tax_amount');
            }
            
            if (!Schema::hasColumn('pos_sales', 'sgst_amount')) {
                $table->decimal('sgst_amount', 12, 2)->default(0)->after('cgst_amount');
            }
            
            $table->text('tax_notes')->nullable()->after('sgst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropColumn([
                'custom_tax_enabled',
                'custom_tax_amount',
                'tax_notes'
            ]);
            
            // Only drop CGST and SGST if they were added by this migration
            if (Schema::hasColumn('pos_sales', 'cgst_amount')) {
                $table->dropColumn('cgst_amount');
            }
            
            if (Schema::hasColumn('pos_sales', 'sgst_amount')) {
                $table->dropColumn('sgst_amount');
            }
        });
    }
};
