<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablasRelacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('catalogo_tipo_vehiculo_id')->unsigned();
            $table->smallInteger('marca_id')->unsigned();
            $table->smallInteger('placa_pais')->unsigned();
            $table->smallInteger('no_ocupantes')->unsigned();
            $table->smallInteger('color')->unsigned();
            $table->smallInteger('con_placas')->unsigned();
            $table->smallInteger('entidad_placas')->unsigned();
            $table->smallInteger('no_placa')->unsigned();
            $table->smallInteger('otro_vehiculo')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('tipo_accidente', function (Blueprint $table) {
            $table->smallInteger('tipo_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('causa_accidente', function (Blueprint $table) {
            $table->smallInteger('tipo_causa_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('causa_peaton', function (Blueprint $table) {
            $table->smallInteger('causa_peaton_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('causa_conductor', function (Blueprint $table) {
            $table->smallInteger('causa_conductor_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('causa_conductor_detalles', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('sexo_id')->unsigned();
            $table->smallInteger('alcoholico')->unsigned();
            $table->smallInteger('cinturon')->unsigned();
            $table->smallInteger('edad')->unsigned();
            $table->smallInteger('ignora_edad')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('victimas_lesionados', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->string('nombre', 50);
            $table->string('apellido_paterno', 50);
            $table->string('apellido_materno', 50);
            $table->smallInteger('ignora_nombre')->unsigned();
            $table->smallInteger('edad')->unsigned();
            $table->smallInteger('ignora_edad')->unsigned();
            $table->smallInteger('sexo_id')->unsigned();
            $table->smallInteger('tipo_usuario_id')->unsigned();
            $table->smallInteger('ubicacion')->unsigned();
            $table->smallInteger('catalogo_clues')->unsigned();
            $table->smallInteger('tipo_id')->unsigned();
            $table->smallInteger('acta_certificacion_id')->unsigned();
            $table->smallInteger('no_acta_certificacion')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('falla_vehiculo', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('falla_vehiculo_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('agente_natural', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('agente_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('condicion_camino', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('condicion_camino_id')->unsigned();
            $table->softDeletes();
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
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('tipo_accidente');
        Schema::dropIfExists('causa_accidente');
        Schema::dropIfExists('causa_peaton');
        Schema::dropIfExists('causa_conductor');
        Schema::dropIfExists('causa_conductor_detalles');
        Schema::dropIfExists('victimas_lesionados');
        Schema::dropIfExists('falla_vehiculo');
        Schema::dropIfExists('agente_natural');
        Schema::dropIfExists('condicion_camino');
    }
}
