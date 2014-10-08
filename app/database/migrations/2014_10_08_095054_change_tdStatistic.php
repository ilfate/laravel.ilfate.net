<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTdStatistic extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename('tdStatistics', 'td_statistic');

		Schema::table('td_statistic', function($table)
		{
		    $table->dropColumn('userData');
		    $table->string('ip', 16);
		    $table->string('laravel_session', 100);
		    $table->string('name', 30)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
