<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing payment methods with null values to have proper defaults
        DB::table('payment_methods')
            ->whereNull('minimum_amount')
            ->update(['minimum_amount' => 0.00]);
            
        DB::table('payment_methods')
            ->whereNull('extra_charge')
            ->update(['extra_charge' => 0.00]);
            
        DB::table('payment_methods')
            ->whereNull('extra_charge_percentage')
            ->update(['extra_charge_percentage' => 0.00]);
            
        DB::table('payment_methods')
            ->whereNull('sort_order')
            ->update(['sort_order' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only fixes data, no need to reverse
    }
};
