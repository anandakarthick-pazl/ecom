<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            // Add flash offer specific fields
            $table->boolean('is_flash_offer')->default(false)->after('is_active');
            $table->string('banner_image')->nullable()->after('is_flash_offer');
            $table->text('banner_title')->nullable()->after('banner_image');
            $table->text('banner_description')->nullable()->after('banner_title');
            $table->string('banner_button_text')->default('Shop Now')->after('banner_description');
            $table->string('banner_button_url')->nullable()->after('banner_button_text');
            $table->boolean('show_popup')->default(true)->after('banner_button_url');
            $table->integer('popup_delay')->default(3000)->after('show_popup'); // milliseconds
            $table->boolean('show_countdown')->default(true)->after('popup_delay');
            $table->string('countdown_text')->default('Hurry! Limited time offer')->after('show_countdown');
            
            // Add index for flash offers
            $table->index(['is_flash_offer', 'is_active', 'start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['is_flash_offer', 'is_active', 'start_date', 'end_date']);
            $table->dropColumn([
                'is_flash_offer',
                'banner_image', 
                'banner_title',
                'banner_description',
                'banner_button_text',
                'banner_button_url',
                'show_popup',
                'popup_delay',
                'show_countdown',
                'countdown_text'
            ]);
        });
    }
};
