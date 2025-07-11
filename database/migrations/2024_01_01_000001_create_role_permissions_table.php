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
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
                $table->timestamps();
                
                // Ensure unique role-permission combinations
                $table->unique(['role_id', 'permission_id']);
                
                // Add indexes for performance
                $table->index(['role_id']);
                $table->index(['permission_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
