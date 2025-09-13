<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            // Add converted status to enum
            DB::statement("ALTER TABLE estimates MODIFY COLUMN status ENUM('draft', 'sent', 'accepted', 'rejected', 'expired', 'converted') DEFAULT 'draft'");
            
            // Add conversion tracking fields
            $table->timestamp('converted_at')->nullable()->after('accepted_at');
            $table->unsignedBigInteger('converted_to_sale_id')->nullable()->after('converted_at');
            
            // Add index for faster lookups
            $table->index('converted_to_sale_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            // Remove the added columns
            $table->dropIndex(['converted_to_sale_id']);
            $table->dropColumn(['converted_at', 'converted_to_sale_id']);
            
            // Revert status enum (set any 'converted' status to 'accepted' first)
            DB::statement("UPDATE estimates SET status = 'accepted' WHERE status = 'converted'");
            DB::statement("ALTER TABLE estimates MODIFY COLUMN status ENUM('draft', 'sent', 'accepted', 'rejected', 'expired') DEFAULT 'draft'");
        });
    }
};
