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
        Schema::create('rel_vehiculos', function (Blueprint $table) {
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
        Schema::create('rel_tipo_accidente', function (Blueprint $table) {
            $table->smallInteger('tipo_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_causa_accidente', function (Blueprint $table) {
            $table->smallInteger('tipo_causa_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_causa_peaton', function (Blueprint $table) {
            $table->smallInteger('causa_peaton_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_causa_conductor', function (Blueprint $table) {
            $table->smallInteger('causa_conductor_id')->unsigned();
            $table->smallInteger('lesion_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_causa_conductor_detalles', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('sexo_id')->unsigned();
            $table->smallInteger('alcoholico')->unsigned();
            $table->smallInteger('cinturon')->unsigned();
            $table->smallInteger('edad')->unsigned();
            $table->smallInteger('ignora_edad')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_victimas_lesionados', function (Blueprint $table) {
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
        Schema::create('rel_falla_vehiculo', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('falla_vehiculo_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_agente_natural', function (Blueprint $table) {
            $table->smallInteger('lesion_id')->unsigned();
            $table->smallInteger('agente_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_condicion_camino', function (Blueprint $table) {
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
        Schema::dropIfExists('rel_vehiculos');
        Schema::dropIfExists('rel_tipo_accidente');
        Schema::dropIfExists('rel_causa_accidente');
        Schema::dropIfExists('rel_causa_peaton');
        Schema::dropIfExists('rel_causa_conductor');
        Schema::dropIfExists('rel_causa_conductor_detalles');
        Schema::dropIfExists('rel_victimas_lesionados');
        Schema::dropIfExists('rel_falla_vehiculo');
        Schema::dropIfExists('rel_agente_natural');
        Schema::dropIfExists('rel_condicion_camino');
    }
}
