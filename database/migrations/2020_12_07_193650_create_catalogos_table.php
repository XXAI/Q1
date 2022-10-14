<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogo_entidades', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
			$table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_municipios', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_localidades', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->smallInteger('catalogo_municipios_id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('catalogo_localidades', function($table) {
            $table->foreign('catalogo_municipios_id')->references('id')->on('catalogo_municipios')->onUpdate('cascade');
        });

        Schema::create('catalogo_vehiculos', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });
        Schema::create('catalogo_marcas', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->smallInteger('catalogo_vehiculo_id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('catalogo_marcas', function($table) {
            $table->foreign('catalogo_vehiculo_id')->references('id')->on('catalogo_vehiculos')->onUpdate('cascade');
        });
  
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogo_localidades');
        Schema::dropIfExists('catalogo_municipios');
        Schema::dropIfExists('catalogo_entidades');
        //Schema::dropIfExists('catalogo_clues');
        Schema::table('catalogo_marcas', function($table) {
            $table->dropForeign(['catalogo_vehiculo_id']);
        });
        
        Schema::dropIfExists('catalogo_marcas');
        Schema::dropIfExists('catalogo_vehiculos');

    }
}
