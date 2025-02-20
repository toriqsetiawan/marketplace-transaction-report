<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonomyTable extends Migration {

	public function up()
	{
		Schema::create('taxonomy', function(Blueprint $table) {
			$table->increments('id');
			$table->string('nama', 255);
			$table->string('slug', 255);
			$table->enum('type', ['nominal', 'satuan']);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('taxonomy');
	}
}
