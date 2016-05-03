<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManyToManyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magazines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('magazine_customer', function (Blueprint $table)
        {
            $table->increments('id');

            $table->integer('magazine_id')->unsigned()->index();
            $table->integer('customer_id')->unsigned()->index();

            $table->foreign('magazine_id')->references('id')->on('magazines');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('magazines');
        Schema::drop('customers');
        Schema::drop('magazine_customer');
    }
}
