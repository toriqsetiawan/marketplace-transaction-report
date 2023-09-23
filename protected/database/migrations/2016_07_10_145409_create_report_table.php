<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportTable extends Migration {

	public function up()
	{
		Schema::create('report', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('employee_id');
			$table->enum('type', array('setor', 'bon'));
			$table->integer('kodi');
			$table->float('total');
			$table->integer('count');
			$table->date('date_at');
			$table->text('description');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('report');
	}
}
