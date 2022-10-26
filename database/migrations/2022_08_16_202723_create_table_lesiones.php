<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLesiones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesiones', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->smallInteger('entidad_federativa_id')->unsigned();
            $table->smallInteger('municipio_id')->unsigned();
            $table->smallInteger('localidad_id')->unsigned();
            $table->smallInteger('zona_id')->unsigned();
            $table->date('fecha');
            $table->time('hora');
            $table->string('colonia')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero')->nullable();
            $table->smallInteger('cp')->nullable();
			$table->decimal('latitud', 8, 6);
			$table->decimal('longitud', 8, 6);
            
            $table->smallInteger('estatal_id')->unsigned();
            $table->smallInteger('interseccion_id')->unsigned()->nullable();
            $table->string('punto_referencia')->nullable();
            $table->string('calle1')->nullable();
            $table->string('calle2')->nullable();
            $table->smallInteger('tipo_pavimentado')->unsigned()->nullable();
            $table->smallInteger('tipo_camino')->unsigned()->nullable();
            $table->smallInteger('via_id')->unsigned()->nullable();
            $table->smallInteger('tipo_via_id')->unsigned()->nullable();
            $table->smallInteger('no_vehiculos')->default(0)->unsigned();
            
            $table->string('otro_tipo_via')->nullable();
            $table->string('otro_tipo_camino');
            $table->string('otro_causa_conductor');
            $table->string('otro_causa_peaton');
            $table->string('otro_falla_accidente');
            $table->string('otro_condicion');
            $table->string('otro_agente_camino');
            $table->string('otro_tipo_accidente');
            $table->bigInteger('user_id');

            
            
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('lesiones', function($table) {
            $table->foreign('entidad_federativa_id')->references('id')->on('catalogo_entidades');
            $table->foreign('municipio_id')->references('id')->on('catalogo_municipios');
            //$table->foreign('localidad_id')->references('id')->on('catalogo_localidades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesiones');
    }
}