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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->string('type'); // razorpay, cod, bank_transfer, upi
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            // Razorpay specific fields
            $table->string('razorpay_key_id')->nullable();
            $table->text('razorpay_key_secret')->nullable(); // Will be encrypted
            $table->string('razorpay_webhook_secret')->nullable();
            
            // Bank Transfer specific fields
            $table->text('bank_details')->nullable(); // JSON field for bank account details
            
            // UPI specific fields
            $table->string('upi_id')->nullable();
            $table->string('upi_qr_code')->nullable(); // Path to QR code image
            
            // Additional settings
            $table->json('settings')->nullable(); // For any additional method-specific settings
            $table->decimal('minimum_amount', 10, 2)->default(0.00);
            $table->decimal('maximum_amount', 10, 2)->nullable();
            $table->decimal('extra_charge', 10, 2)->default(0.00); // Fixed charge
            $table->decimal('extra_charge_percentage', 5, 2)->default(0.00); // Percentage charge
            
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index(['company_id', 'is_active']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
