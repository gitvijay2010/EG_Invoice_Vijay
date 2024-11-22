<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Reference to the order
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Reference to the product
            $table->integer('quantity'); // Quantity of the product in the order
            $table->decimal('price', 8, 2); // Price of the product at the time of purchase
            $table->decimal('subtotal', 8, 2); // Subtotal for this product (quantity * price)
            $table->decimal('tax', 8, 2); // Tax applied to this item
            $table->decimal('discount', 8, 2)->default(0.00); // Discount per item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
