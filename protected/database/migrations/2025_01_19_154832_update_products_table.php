<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'ukuran', 'harga_tambahan', 'harga_online', 'harga_offline', 'harga_mitra']);
            $table->decimal('harga_jual', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable();
            $table->string('ukuran')->nullable();
            $table->decimal('harga_tambahan', 10, 2)->nullable();
            $table->decimal('harga_online', 10, 2)->nullable();
            $table->decimal('harga_offline', 10, 2)->nullable();
            $table->decimal('harga_mitra', 10, 2)->nullable();
            $table->dropColumn(['harga_jual']);
        });
    }
}
