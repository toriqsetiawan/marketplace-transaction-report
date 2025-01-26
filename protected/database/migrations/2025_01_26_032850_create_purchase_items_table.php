<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id'); // Reference to purchases
            $table->unsignedBigInteger('product_variant_id'); // Reference to product variants
            $table->integer('quantity'); // Quantity purchased
            $table->decimal('unit_price', 15, 2); // Price per unit
            $table->decimal('subtotal', 15, 2); // Subtotal cost for the item
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_items');
    }
}
