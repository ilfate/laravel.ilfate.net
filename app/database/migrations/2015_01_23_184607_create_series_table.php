<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('series');
        Schema::create('series', function($table)
        {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('studio_id')->nullable();
            $table->integer('year');
            $table->integer('difficulty');
            $table->timestamps();

            $table->unique('name');

        });

        Schema::dropIfExists('images');
        Schema::create('images', function($table)
        {
            $table->increments('id');
            $table->binary('image');
            $table->string('url', 24);
            $table->integer('series_id');
            $table->integer('actor_id')->nullable();
            $table->integer('difficulty');
            $table->timestamps();
        });

        Schema::dropIfExists('actors');
        Schema::create('actors', function($table)
        {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('image', 40)->nullable();
            $table->integer('difficulty');
            $table->timestamps();
        });

        Schema::dropIfExists('series_types');
        Schema::create('series_types', function($table)
        {
            $table->increments('id');
            $table->integer('series_id');
            $table->integer('type');
            $table->timestamps();
        });

        Schema::dropIfExists('studios');
        Schema::create('studios', function($table)
        {
            $table->increments('id');
            $table->string('name', 20);
            $table->timestamps();
        });

        Schema::table('users', function($table) {

            $table->integer('rights')->default(1);

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
