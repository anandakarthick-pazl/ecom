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
        Schema::create('upload_logs', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();
            $table->enum('storage_type', ['local', 's3'])->default('local');
            $table->enum('upload_type', ['product', 'category', 'banner', 'general'])->default('general');
            $table->unsignedBigInteger('source_id')->nullable(); // ID of the related model
            $table->string('source_type')->nullable(); // Model class name
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->json('meta_data')->nullable(); // Additional metadata
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['upload_type', 'source_id']);
            $table->index(['company_id', 'upload_type']);
            $table->index(['uploaded_by']);
            $table->index(['created_at']);

            // Foreign keys
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_logs');
    }
};
