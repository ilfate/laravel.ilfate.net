<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('cards');
        Schema::create('cards', function($table)
        {
            $table->increments('id');

            $table->integer('card_id');
            $table->integer('player_id');
            $table->integer('kills')->default(0);
            $table->integer('games')->default(0);
            $table->integer('wins')->default(0);

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
