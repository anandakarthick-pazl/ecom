<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('landing_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->string('key');
            $table->json('value');
            $table->enum('type', ['text', 'image', 'array', 'boolean'])->default('text');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['section', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('landing_page_settings');
    }
};
