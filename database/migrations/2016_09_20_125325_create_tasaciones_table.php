<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasaciones', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('id_cli');
            $table->integer('id_tipo_oper');
            $table->integer('id_tipo_prop');
            $table->integer('id_ubica');
            $table->boolean('estado');
            $table->integer('id_moneda');
            $table->decimal('valor_moneda');

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
