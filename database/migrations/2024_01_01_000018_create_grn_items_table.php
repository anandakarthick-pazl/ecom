<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('purchase_order_item_id');
            $table->integer('ordered_quantity');
            $table->integer('received_quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->foreign('grn_id')->references('id')->on('goods_receipt_notes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grn_items');
    }
};
