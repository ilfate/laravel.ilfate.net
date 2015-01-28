<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSeries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('series', function($table) {

		    $table->integer('active')->default(0);
		    $table->index('active');
		    
		});
		Schema::table('images', function($table) {

		    $table->index('difficulty');
		    $table->index(array('series_id', 'difficulty'));
		    
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
