<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesStats extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('images_stats');
        Schema::create('images_stats', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->integer('images_id')->unsigned();
            $table->integer('type')->unsigned()->nullable();
            $table->timestamps();
            $table->index('images_id');
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
