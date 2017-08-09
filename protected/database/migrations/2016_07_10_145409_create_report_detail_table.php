<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportDetailTable extends Migration {

	public function up()
	{
		Schema::create('report_detail', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('varian_id');
			$table->bigInteger('report_id');
			$table->integer('quantity');
			$table->float('price_history');
			$table->float('sub_total');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('report_detail');
	}
}