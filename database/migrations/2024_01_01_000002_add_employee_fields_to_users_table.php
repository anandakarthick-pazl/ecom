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
        Schema::table('users', function (Blueprint $table) {
            // Add employee-specific columns if they don't exist
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->unique()->after('id');
            }
            
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null')->after('role');
            }
            
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('role_id');
            }
            
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('hire_date');
            }
            
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable()->after('department');
            }
            
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('designation');
            }
            
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('salary');
            }
            
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('permissions');
            }
            
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('avatar');
            }
            
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToCheck = [
                'employee_id', 'role_id', 'hire_date', 'department', 
                'designation', 'salary', 'permissions', 'avatar', 
                'phone', 'status', 'last_login_at'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
