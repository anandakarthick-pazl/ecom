<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('mobile')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('pincode');
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->integer('credit_days')->default(30);
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
};
