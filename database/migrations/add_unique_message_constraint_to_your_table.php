<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
         DB::statement('TRUNCATE TABLE notifications');
        Schema::table('notifications', function (Blueprint $table) {
            // Add a hash column for uniqueness (safe for longtext)
            if (!Schema::hasColumn('notifications', 'message_hash')) {
                $table->char('message_hash', 40)->nullable();
            }
        });

        // Populate hash column for existing rows
        DB::statement("UPDATE notifications SET message_hash = SHA1(message)");

        // Add unique index on hash column
        Schema::table('notifications', function (Blueprint $table) {
            $table->unique('message_hash', 'unique_message_hash');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropUnique('unique_message_hash');
            $table->dropColumn('message_hash');
        });
    }
};
