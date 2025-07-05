<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('sale_date');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'upi', 'mixed'])->default('cash');
            $table->enum('status', ['completed', 'refunded', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('cashier_id');
            $table->timestamps();
            
            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'sale_date']);
            $table->index('invoice_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pos_sales');
    }
};
