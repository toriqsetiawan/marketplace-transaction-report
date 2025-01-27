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
            $table->unsignedBigInteger('supplier_id'); // Reference to suppliers
            $table->unsignedBigInteger('user_id'); // Reference to user who made the purchase
            $table->string('purchase_code')->unique(); // Unique code for the purchase
            $table->date('purchase_date'); // Date of the purchase
            $table->decimal('total_cost', 15, 2); // Total cost of the purchase
            $table->enum('status', ['pending', 'completed', 'cancelled']); // Purchase status
            $table->string('note')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
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
