<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('variable_price_id');
            $table->integer('jumlah');
            $table->integer('ukuran');
            $table->string('motif', 30);
            $table->float('harga_beli');
            $table->float('harga_jual');
            $table->float('biaya_tambahan')->default(0);
            $table->float('biaya_lain_lain')->default(0);
            $table->float('pajak')->default(0);
            $table->float('total_paid');
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
