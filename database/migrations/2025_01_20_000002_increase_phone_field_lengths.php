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
        Schema::table('companies', function (Blueprint $table) {
            // Increase phone field lengths to accommodate international numbers with formatting
            $table->string('phone', 30)->nullable()->change();
            $table->string('whatsapp_number', 30)->nullable()->change();
            $table->string('mobile_number', 30)->nullable()->change();
            $table->string('alternate_phone', 30)->nullable()->change();

            // Add gpay_number field if it doesn't exist, or modify if it does
            if (!Schema::hasColumn('companies', 'gpay_number')) {
                $table->string('gpay_number', 30)->nullable()->after('alternate_phone')
                    ->comment('Google Pay number for digital payments');
            } else {
                $table->string('gpay_number', 30)->nullable()->change();
            }
            Schema::table('companies', function (Blueprint $table) {
                $table->string('phone', 50)->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Revert back to original lengths
            $table->string('phone', 20)->nullable()->change();
            $table->string('whatsapp_number', 20)->nullable()->change();
            $table->string('mobile_number', 20)->nullable()->change();
            $table->string('alternate_phone', 20)->nullable()->change();
            $table->string('gpay_number', 20)->nullable()->change();
        });
    }
};
