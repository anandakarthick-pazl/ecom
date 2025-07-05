<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'manager', 'staff'])->default('admin');
            $table->boolean('is_super_admin')->default(false);
            // $table->string('avatar')->nullable();
            // $table->string('phone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('company_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id', 'role', 'is_super_admin', 'avatar', 
                'phone', 'status', 'last_login_at'
            ]);
        });
    }
};
