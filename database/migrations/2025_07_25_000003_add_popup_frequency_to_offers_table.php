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
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('popup_frequency', ['always', 'once_per_session', 'once_per_day', 'once_per_week'])
                  ->default('always')
                  ->after('show_popup')
                  ->comment('How often to show the popup: always, once_per_session, once_per_day, once_per_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('popup_frequency');
        });
    }
};
