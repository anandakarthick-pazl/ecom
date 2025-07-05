<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->json('color_scheme')->nullable()->after('settings');
            $table->string('layout_type')->nullable()->after('color_scheme');
            $table->json('components')->nullable()->after('layout_type');
            $table->json('screenshots')->nullable()->after('components');
            $table->json('tags')->nullable()->after('screenshots');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner')->after('tags');
            $table->boolean('responsive')->default(true)->after('difficulty_level');
            $table->boolean('rtl_support')->default(false)->after('responsive');
            $table->boolean('dark_mode')->default(false)->after('rtl_support');
            $table->string('author')->nullable()->after('dark_mode');
            $table->decimal('rating', 2, 1)->default(0)->after('author');
            $table->integer('downloads_count')->default(0)->after('rating');
        });
    }

    public function down()
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn([
                'color_scheme',
                'layout_type',
                'components',
                'screenshots',
                'tags',
                'difficulty_level',
                'responsive',
                'rtl_support',
                'dark_mode',
                'author',
                'rating',
                'downloads_count'
            ]);
        });
    }
};
