<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreeateTableClientesFavoritos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorito_propiedad', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('id_prop');
            $table->integer('id_cli');
            $table->boolean('favorite');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorito_propiedad', function(Blueprint $table) {
            $table->drop('favorito_propiedad');
        });
    }
}
