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
        Schema::table('estimates', function (Blueprint $table) {
            // Make customer_phone nullable to fix the constraint violation
            if (Schema::hasColumn('estimates', 'customer_phone')) {
                $table->string('customer_phone', 20)->nullable()->change();
            }
            
            // Also ensure other customer fields are nullable for flexibility
            if (Schema::hasColumn('estimates', 'customer_email')) {
                $table->string('customer_email')->nullable()->change();
            }
            
            if (Schema::hasColumn('estimates', 'customer_address')) {
                $table->text('customer_address')->nullable()->change();
            }
            
            // Make notes and terms_conditions nullable as well
            if (Schema::hasColumn('estimates', 'notes')) {
                $table->text('notes')->nullable()->change();
            }
            
            if (Schema::hasColumn('estimates', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            // Revert changes - make fields non-nullable again
            if (Schema::hasColumn('estimates', 'customer_phone')) {
                $table->string('customer_phone', 20)->nullable(false)->change();
            }
            
            if (Schema::hasColumn('estimates', 'customer_email')) {
                $table->string('customer_email')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('estimates', 'customer_address')) {
                $table->text('customer_address')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('estimates', 'notes')) {
                $table->text('notes')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('estimates', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable(false)->change();
            }
        });
    }
};
