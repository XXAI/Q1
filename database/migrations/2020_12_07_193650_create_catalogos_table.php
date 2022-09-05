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
            $table->string('clave', 5); 
			$table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_localidades', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->smallInteger('catalogo_municipios_id')->unsigned();
            $table->string('clave', 5);
			$table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('catalogo_localidades', function($table) {
            $table->foreign('catalogo_municipios_id')->references('id')->on('catalogo_municipios')->onUpdate('cascade');
        });

        Schema::create('catalogo_clues', function (Blueprint $table) {

            $table->string('id', 12)->primary()->comment('CLave Unica de Establecimientos de Salud')->index();
            $table->string('descripcion', 120)->comment('Nombre de la unidad de salud')->index();
			$table->string('tipologia', 70);
            $table->string('nivelAtencion', 50);     
            $table->decimal('latitud', 8,6);     
            $table->decimal('longitud', 8,6);     
            $table->timestamps();
            $table->softDeletes();       
            
        });

        Schema::create('catalogo_vehiculos', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });
        Schema::create('catalogo_marca', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->smallInteger('catalogo_vehiculo_id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('catalogo_marca', function($table) {
            $table->foreign('catalogo_vehiculo_id')->references('id')->on('catalogo_vehiculos')->onUpdate('cascade');
        });
        Schema::create('catalogo_tipo_camino', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_tipo_accidente', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_causas_accidentes', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_causas_conductor', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_causas_peaton', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_causas_falla', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });
        Schema::create('catalogo_causas_condicion_camino', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });
        Schema::create('catalogo_causas_agentes_naturales', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::create('catalogo_usuario_vias', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
            $table->string('descripcion', 100);
			$table->timestamps();
			$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogo_entidades');
        Schema::dropIfExists('catalogo_municipios');
        Schema::dropIfExists('catalogo_localidades');
        Schema::dropIfExists('catalogo_clues');
        Schema::dropIfExists('catalogo_vehiculos');
        Schema::dropIfExists('catalogo_marca');
        Schema::dropIfExists('catalogo_tipo_camino');
        Schema::dropIfExists('catalogo_tipo_accidente');
        Schema::dropIfExists('catalogo_causas_accidentes');
        Schema::dropIfExists('catalogo_causas_conductor');
        Schema::dropIfExists('catalogo_causas_peaton');
        Schema::dropIfExists('catalogo_causas_peaton');
        Schema::dropIfExists('catalogo_causas_falla');
        Schema::dropIfExists('catalogo_causas_condicion_camino');
        Schema::dropIfExists('catalogo_causas_agentes_naturales');
        Schema::dropIfExists('catalogo_usuario_vias');

    }
}
