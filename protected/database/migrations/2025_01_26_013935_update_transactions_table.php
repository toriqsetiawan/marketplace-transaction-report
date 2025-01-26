<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // remove existing columns
            $table->dropColumn([
                'name', 'marketplace', 'jumlah', 'ukuran', 'motif', 'harga_beli',
                'harga_jual', 'biaya_tambahan', 'biaya_lain_lain', 'pajak', 'total_paid', 'keterangan'
            ]);

            // new structure
            $table->unsignedBigInteger('user_id'); // The user who made the transaction
            $table->string('status')->default('pending')->change(); // pending/paid/cancel/return
            $table->string('type')->default('offline'); // 'online' or 'offline'
            $table->decimal('total_price', 10, 2); // Total price for the transaction
            $table->decimal('packing_cost', 10, 2)->default(0); // Packing cost for the transaction

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
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'status', 'type', 'total_price', 'packing_cost']);
            $table->string('name')->after('channel')->nullable();
            $table->string('marketplace')->after('name')->nullable();
            $table->integer('jumlah')->after('marketplace')->default(0);
            $table->integer('ukuran')->after('jumlah')->default(0);
            $table->string('motif')->after('ukuran')->nullable();
            $table->decimal('harga_beli', 10, 2)->after('motif')->default(0);
            $table->decimal('harga_jual', 10, 2)->after('harga_beli')->default(0);
            $table->decimal('biaya_tambahan', 10, 2)->after('harga_jual')->default(0);
            $table->decimal('biaya_lain_lain', 10, 2)->after('biaya_tambahan')->default(0);
            $table->decimal('pajak', 10, 2)->after('biaya_lain_lain')->default(0);
            $table->decimal('total_paid', 10, 2)->after('pajak')->default(0);
            $table->boolean('status')->default(1)->comment('1:pending, 2:lunas, 3:retur')->change();
            $table->text('keterangan')->after('total_paid')->nullable();
        });
    }
}
