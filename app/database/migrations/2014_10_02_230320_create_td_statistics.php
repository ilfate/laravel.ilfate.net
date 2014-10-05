<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTdStatistics extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('tdStatistics', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('pointsEarned')->unsigned();
            $table->integer('turnsSurvived')->unsigned();
            $table->integer('unitsKilled')->unsigned();

            $table->text('userData');

            $table->dateTime('created_at');
            $table->dateTime('updated_at');

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
