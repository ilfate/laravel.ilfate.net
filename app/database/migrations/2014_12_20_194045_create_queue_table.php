<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('queue');
        Schema::create('queue', function($table)
        {
            $table->increments('id');

            $table->integer('player_id');
            $table->integer('deck_id');
            $table->integer('power')->default(0);
            $table->integer('game_type_id')->default(0);

            $table->index('player_id');

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
