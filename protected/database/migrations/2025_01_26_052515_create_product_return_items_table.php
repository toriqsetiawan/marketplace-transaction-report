<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_return_id'); // Reference to the product_returns table
            $table->unsignedBigInteger('transaction_item_id'); // Reference to the transaction item
            $table->integer('quantity'); // Quantity returned
            $table->decimal('refund_amount', 15, 2)->nullable(); // Refund amount for this item
            $table->timestamps();

            $table->foreign('product_return_id')->references('id')->on('product_returns')->onDelete('cascade');
            $table->foreign('transaction_item_id')->references('id')->on('transaction_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_return_items');
    }
}
