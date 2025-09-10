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
            $table->string('whatsapp_number')->nullable()->after('phone')
                  ->comment('WhatsApp contact number for customer support');
            $table->string('mobile_number')->nullable()->after('whatsapp_number')
                  ->comment('Primary mobile contact number');
            $table->string('alternate_phone')->nullable()->after('mobile_number')
                  ->comment('Alternate/secondary phone number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'mobile_number', 'alternate_phone']);
        });
    }
};
