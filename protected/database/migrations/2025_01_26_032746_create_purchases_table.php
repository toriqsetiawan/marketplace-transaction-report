<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Reference to user who made the purchase
            $table->string('purchase_code')->unique(); // Unique code for the purchase
            $table->date('purchase_date'); // Date of the purchase
            $table->decimal('total_price', 10, 2); // Total cost of the purchase
            $table->enum('status', ['pending', 'complete', 'cancel']); // Purchase status
            $table->string('note')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
