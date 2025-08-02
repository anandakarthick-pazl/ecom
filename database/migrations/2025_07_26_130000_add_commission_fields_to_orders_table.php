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
        Schema::table('orders', function (Blueprint $table) {
            // Commission fields for online orders
            $table->boolean('commission_enabled')->default(false)->after('paid_at');
            $table->string('reference_name')->nullable()->after('commission_enabled');
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('reference_name');
            $table->text('commission_notes')->nullable()->after('commission_percentage');
            
            // Add index for commission queries
            $table->index(['commission_enabled', 'reference_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['commission_enabled', 'reference_name']);
            $table->dropColumn(['commission_enabled', 'reference_name', 'commission_percentage', 'commission_notes']);
        });
    }
};
