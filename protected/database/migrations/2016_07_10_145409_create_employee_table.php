<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTable extends Migration {

	public function up()
	{
		Schema::create('employee', function(Blueprint $table) {
			$table->increments('id');
			$table->string('nama', 255);
			$table->string('alamat', 255);
			$table->string('phone', 15);
			$table->enum('golongan', ['mingguan', 'bulanan']);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('employee');
	}
}
