<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuessStatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('guess_stats');
        Schema::create('guess_stats', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('points')->unsigned();
            $table->integer('answers')->unsigned();
            $table->integer('bonuses')->unsigned()->nullable();
            $table->string('ip', 16);
            $table->string('laravel_session', 100);
            $table->string('name', 30)->nullable();
            $table->timestamps();
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
