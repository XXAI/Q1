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
            $table->smallInteger('lesiones_id')->unsigned();
            $table->smallInteger('catalogo_tipo_vehiculo_id')->unsigned();
            $table->string('otro_tipo_vehiculo', 200);
            $table->smallInteger('marca_id')->unsigned();
            $table->smallInteger('uso_vehiculo')->unsigned();
            $table->smallInteger('puesto_disposicion')->unsigned();
            $table->smallInteger('placa_pais')->unsigned()->nullable();
            $table->smallInteger('no_ocupantes')->unsigned();
            $table->string('color',50);
            $table->string('modelo',50);
            $table->smallInteger('con_placas')->unsigned();
            $table->smallInteger('entidad_placas')->unsigned()->nullable();
            $table->smallInteger('no_placa')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_tipo_accidente', function (Blueprint $table) {
            $table->smallInteger('rel_tipo_accidente_id')->unsigned();
            $table->smallInteger('lesiones_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::create('rel_causa_pasajero', function (Blueprint $table) {
            $table->text('causa_pasajero');
            $table->smallInteger('lesiones_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::create('rel_causa_accidente', function (Blueprint $table) {
            $table->smallInteger('rel_causa_accidente_id')->unsigned();
            $table->smallInteger('lesiones_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_causa_peaton', function (Blueprint $table) {
            $table->smallInteger('rel_causa_peaton_id')->unsigned();
            $table->smallInteger('lesiones_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_causa_conductor', function (Blueprint $table) {
            $table->smallInteger('rel_causa_conductor_id')->unsigned();
            $table->smallInteger('lesiones_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_causa_conductor_detalles', function (Blueprint $table) {
            $table->smallInteger('lesiones_id')->unsigned();
            $table->smallInteger('sexo_id')->unsigned();
            $table->smallInteger('alcoholico')->unsigned();
            $table->smallInteger('cinturon')->unsigned();
            $table->smallInteger('edad')->unsigned();
            $table->smallInteger('ignora_edad')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_victimas_lesionados', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->smallInteger('lesiones_id')->unsigned();
            $table->smallInteger('tipo_id')->unsigned();
            $table->smallInteger('acta_certificacion_id')->unsigned()->nullable();
            $table->string('no_acta_certificacion',50)->nullable();
            $table->string('nombre', 50)->nullable();
            $table->string('apellido_paterno', 50)->nullable();
            $table->string('apellido_materno', 50)->nullable();
            $table->smallInteger('edad')->unsigned()->nullable();
            $table->smallInteger('sexo_id')->unsigned();
            $table->smallInteger('tipo_usuario_id')->unsigned();
            $table->string('hospitalizado',200);
            $table->smallInteger('casco')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

        });
        Schema::create('rel_lesion_parte', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('rel_victimas_lesionados_id')->unsigned();
            $table->smallInteger('orientacion')->unsigned();
            $table->smallInteger('plano')->unsigned();
            $table->smallInteger('parte')->unsigned();
           
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('rel_victimas_lesionados_id')->references('id')->on('rel_victimas_lesionados')->onDelete('cascade');

        });

        Schema::create('rel_lesion_parte_tipo', function (Blueprint $table) {
            $table->bigInteger('rel_lesion_parte_id')->unsigned();
            $table->smallInteger('opcion')->unsigned();
            
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('rel_lesion_parte_id')->references('id')->on('rel_lesion_parte')->onDelete('cascade');

        });
        Schema::create('rel_falla_vehiculo', function (Blueprint $table) {
            $table->smallInteger('lesiones_id')->unsigned();
            $table->smallInteger('rel_falla_vehiculo_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_agente_natural', function (Blueprint $table) {
            $table->smallInteger('lesiones_id')->unsigned();
            $table->smallInteger('rel_agente_natural_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rel_condicion_camino', function (Blueprint $table) {
            $table->smallInteger('lesiones_id')->unsigned();
            $table->smallInteger('rel_condicion_camino_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('rel_fotografias', function (Blueprint $table) {
            $table->smallInteger('lesiones_id')->unsigned();
            $table->string('nombre_imagen',25);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('catalogo_clues', function (Blueprint $table) {
            $table->smallInteger('clues')->unsigned();
            $table->smallInteger('catalogo_municipios_id')->unsigned();
            $table->string('institucion',150);
            $table->string('descripcion',150);
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
        Schema::dropIfExists('catalogo_clues');
        Schema::dropIfExists('rel_vehiculos');
        Schema::dropIfExists('rel_tipo_accidente');
        Schema::dropIfExists('rel_causa_pasajero');
        Schema::dropIfExists('rel_causa_accidente');
        Schema::dropIfExists('rel_causa_peaton');
        Schema::dropIfExists('rel_causa_conductor');
        Schema::dropIfExists('rel_causa_conductor_detalles');
        Schema::dropIfExists('rel_lesion_parte_tipo');
        Schema::dropIfExists('rel_lesion_parte');
        Schema::dropIfExists('rel_victimas_lesionados');
        Schema::dropIfExists('rel_falla_vehiculo');
        Schema::dropIfExists('rel_agente_natural');
        Schema::dropIfExists('rel_condicion_camino');
        Schema::dropIfExists('rel_fotografias');
    }
}
