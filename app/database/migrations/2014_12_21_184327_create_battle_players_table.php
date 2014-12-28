<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattlePlayersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('battle_players');
        Schema::create('battle_players', function($table)
        {
            $table->increments('id');

            $table->integer('battle_id');
            $table->integer('player_id');
            $table->integer('deck_id');
            $table->integer('team');

            $table->index('player_id');
            $table->index('battle_id');

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
