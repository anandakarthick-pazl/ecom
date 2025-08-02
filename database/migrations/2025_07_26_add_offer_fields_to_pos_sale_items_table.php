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
            // Add fields to track offer information
            $table->decimal('original_price', 10, 2)->nullable()->after('unit_price');
            $table->string('offer_applied')->nullable()->after('total_amount');
            $table->decimal('offer_savings', 10, 2)->default(0)->after('offer_applied');
            $table->text('notes')->nullable()->after('offer_savings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'offer_applied', 'offer_savings', 'notes']);
        });
    }
};
