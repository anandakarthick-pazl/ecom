<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('price');
            $table->string('barcode')->nullable()->after('sku');
            $table->string('code')->nullable()->after('barcode');
            $table->integer('low_stock_threshold')->default(10)->after('stock');
            
            $table->index(['barcode']);
            $table->index(['code']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropIndex(['code']);
            $table->dropColumn(['cost_price', 'barcode', 'code', 'low_stock_threshold']);
        });
    }
};
