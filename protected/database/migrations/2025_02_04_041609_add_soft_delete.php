<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('attributes', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('attribute_values', function (Blueprint $table) {
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
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
