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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('reference_type')->default('pos_sale'); // pos_sale, order
            $table->unsignedBigInteger('reference_id'); // pos_sale_id or order_id
            $table->string('reference_name'); // Name of the person getting commission
            $table->decimal('commission_percentage', 5, 2); // Up to 999.99%
            $table->decimal('base_amount', 10, 2); // Amount on which commission is calculated
            $table->decimal('commission_amount', 10, 2); // Calculated commission amount
            $table->string('status')->default('pending'); // pending, paid, cancelled
            $table->text('notes')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable(); // User who marked as paid
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['company_id', 'reference_type', 'reference_id']);
            $table->index(['company_id', 'reference_name']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
