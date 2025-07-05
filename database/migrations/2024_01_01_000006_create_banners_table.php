<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('link_url')->nullable();
            $table->enum('position', ['top', 'middle', 'bottom'])->default('top');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('alt_text')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'position', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
