<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add 'flash' to the enum type field
        DB::statement("ALTER TABLE offers MODIFY COLUMN type ENUM('percentage', 'fixed', 'category', 'product', 'flash') NOT NULL");
        
        // Add discount_type field if it doesn't exist
        if (!Schema::hasColumn('offers', 'discount_type')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->enum('discount_type', ['percentage', 'flat'])->nullable()->after('type');
            });
        }
    }

    public function down()
    {
        // Remove 'flash' from enum and revert back
        DB::statement("ALTER TABLE offers MODIFY COLUMN type ENUM('percentage', 'fixed', 'category', 'product') NOT NULL");
        
        // Remove discount_type field
        if (Schema::hasColumn('offers', 'discount_type')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->dropColumn('discount_type');
            });
        }
    }
};
