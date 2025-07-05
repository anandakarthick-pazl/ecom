<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('twilio_account_sid');
            $table->string('twilio_auth_token');
            $table->string('twilio_phone_number')->nullable();
            $table->string('whatsapp_business_number');
            $table->boolean('is_enabled')->default(false);
            $table->text('default_message_template');
            $table->string('test_number')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('webhook_secret')->nullable();
            $table->integer('max_file_size_mb')->default(5);
            $table->json('allowed_file_types')->default('["pdf"]');
            $table->integer('rate_limit_per_minute')->default(10);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Unique constraint to ensure one config per company
            $table->unique('company_id');
            
            // Indexes
            $table->index('company_id');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_configs');
    }
}
