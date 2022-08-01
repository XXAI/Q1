<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accidentes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->string('colonia')->nullable();
            $table->string('calle')->nullable();
            $table->string('numero')->nullable();
			$table->float('latitud', 15, 0);
			$table->float('longitud', 15, 0);
            $table->smallInteger('entidad_federativa_id')->unsigned();
            $table->smallInteger('municipio_id')->unsigned();
            $table->smallInteger('localidad_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('accidentes', function($table) {
            $table->foreign('entidad_federativa_id')->references('id')->on('catalogo_entidades_federativas');
            $table->foreign('municipio_id')->references('id')->on('catalogo_municipios');
            $table->foreign('localidad_id')->references('id')->on('catalogo_localidades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accidente');
    }
}
