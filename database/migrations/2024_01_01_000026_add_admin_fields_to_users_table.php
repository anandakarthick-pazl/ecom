<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('address')->nullable()->after('phone');
            // $table->string('role')->default('admin')->after('address');
            $table->json('preferences')->nullable()->after('address'); // For user preferences
            $table->timestamp('last_login_at')->nullable()->after('preferences');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'phone', 'address', 'role', 'preferences', 'last_login_at']);
        });
    }
};
