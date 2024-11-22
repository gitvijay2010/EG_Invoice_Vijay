<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount_value', 8, 2);
            $table->enum('type', ['flat', 'product']);  // 'flat' for overall discount, 'product' for item-specific
            $table->unsignedBigInteger('product_id')->nullable(); // Nullable for flat discounts
            $table->dateTime('expires_at');
            $table->timestamps();

            // Add foreign key for product_id
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
