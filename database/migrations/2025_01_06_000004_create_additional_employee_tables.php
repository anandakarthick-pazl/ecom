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
        // Employee Activity Log Table
        if (!Schema::hasTable('employee_activity_logs')) {
            Schema::create('employee_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->string('activity_type'); // login, logout, permission_change, etc.
                $table->text('description');
                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['employee_id', 'activity_type']);
                $table->index('created_at');
            });
        }

        // Employee Training Records Table
        if (!Schema::hasTable('employee_training_records')) {
            Schema::create('employee_training_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->string('training_name');
                $table->text('description')->nullable();
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'failed'])->default('not_started');
                $table->date('start_date')->nullable();
                $table->date('completion_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->integer('score')->nullable();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['employee_id', 'status']);
            });
        }

        // Employee Performance Reviews Table
        if (!Schema::hasTable('employee_performance_reviews')) {
            Schema::create('employee_performance_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('reviewer_id');
                $table->string('review_period'); // Q1 2025, Annual 2024, etc.
                $table->date('review_date');
                $table->integer('overall_score')->nullable(); // 1-100
                $table->json('scores')->nullable(); // Different categories scores
                $table->text('strengths')->nullable();
                $table->text('areas_for_improvement')->nullable();
                $table->text('goals')->nullable();
                $table->text('comments')->nullable();
                $table->enum('status', ['draft', 'submitted', 'approved', 'completed'])->default('draft');
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['employee_id', 'review_date']);
            });
        }

        // Role Permission Audit Log Table
        if (!Schema::hasTable('role_permission_audit_logs')) {
            Schema::create('role_permission_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id')->nullable();
                $table->unsignedBigInteger('permission_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable(); // Who made the change
                $table->string('action'); // assigned, revoked, role_created, role_deleted, etc.
                $table->text('description');
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
                
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->index(['role_id', 'action']);
                $table->index(['permission_id', 'action']);
                $table->index('created_at');
            });
        }

        // Employee Attendance Table (Basic)
        if (!Schema::hasTable('employee_attendance')) {
            Schema::create('employee_attendance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->date('date');
                $table->time('clock_in')->nullable();
                $table->time('clock_out')->nullable();
                $table->integer('break_minutes')->default(0);
                $table->integer('total_hours')->nullable(); // in minutes
                $table->enum('status', ['present', 'absent', 'late', 'half_day', 'leave'])->default('present');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['employee_id', 'date']);
                $table->index(['date', 'status']);
            });
        }

        // Ensure role_permissions table has proper structure (it should exist)
        if (Schema::hasTable('role_permissions')) {
            // Add any missing columns to existing table
            Schema::table('role_permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('role_permissions', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendance');
        Schema::dropIfExists('role_permission_audit_logs');
        Schema::dropIfExists('employee_performance_reviews');
        Schema::dropIfExists('employee_training_records');
        Schema::dropIfExists('employee_activity_logs');
    }
};
