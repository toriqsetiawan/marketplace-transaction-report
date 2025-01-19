<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVarianTable extends Migration {

	public function up()
	{
		Schema::create('varian', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('taxonomi_id');
			$table->enum('kode', ['DB', 'KR', 'OT']);
			$table->string('nama', 255);
			$table->enum('type', ['item', 'bon', 'setor']);
			$table->float('harga_satuan');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('varian');
	}
}
