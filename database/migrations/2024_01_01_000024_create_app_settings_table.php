<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, json, file
            $table->string('group')->default('general'); // general, appearance, notifications, etc.
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();
            
            $table->index(['key', 'group']);
            $table->index(['company_id', 'key']);
            $table->unique(['key', 'company_id']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_settings');
    }
};
