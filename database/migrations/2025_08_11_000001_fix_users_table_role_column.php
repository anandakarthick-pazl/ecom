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
            // Add missing columns safely
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 100)->nullable()->after('password');
            } else {
                // Modify existing role column to be longer
                $table->string('role', 100)->nullable()->change();
            }
            
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id', 50)->nullable()->unique()->after('id');
            }
            
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('role');
            }
            
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('role_id');
            }
            
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 100)->nullable()->after('branch_id');
            }
            
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation', 100)->nullable()->after('department');
            }
            
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('designation');
            }
            
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 15, 2)->nullable()->after('hire_date');
            }
            
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('salary');
            }
            
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('avatar');
            }
            
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('company_id');
            }
            
            if (!Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('permissions');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_super_admin');
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
                'employee_id', 'phone', 'role_id', 'branch_id', 
                'department', 'designation', 'hire_date', 'salary', 
                'avatar', 'status', 'company_id', 'permissions', 
                'is_super_admin', 'last_login_at'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
