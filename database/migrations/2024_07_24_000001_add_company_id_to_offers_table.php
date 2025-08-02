<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('used_count');
                $table->index('company_id');
            }
            
            // Also add discount_type for category offers with percentage/flat options
            if (!Schema::hasColumn('offers', 'discount_type')) {
                $table->enum('discount_type', ['percentage', 'flat'])->default('percentage')->after('type');
            }
        });
    }

    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'company_id')) {
                $table->dropColumn('company_id');
            }
            if (Schema::hasColumn('offers', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
        });
    }
};
