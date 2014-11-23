<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists('users');
        Schema::create('users', function($table)
        {
            $table->increments('id', 5);

            $table->string('email', 60);
            $table->string('password', 60);
            $table->string('name', 20)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('last_visit');

            $table->timestamps();

            $table->unique('email');


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
