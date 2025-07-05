<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique();
            $table->date('adjustment_date');
            $table->enum('type', ['increase', 'decrease', 'damage', 'expired', 'theft', 'other']);
            $table->enum('status', ['draft', 'approved', 'cancelled'])->default('draft');
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'adjustment_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
