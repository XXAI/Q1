<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRelTipoUsoVehiculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rel_vehiculos', function($table) {
            $table->smallInteger('tipo_uso_vehiculo_id')->nullable()->after("uso_vehiculo")->unsigned();
            $table->string('otra_marca', 250)->nullable()->after("marca_id");
        });
        Schema::table('rel_victimas_lesionados', function($table) {
            $table->string('diagnostico', 250)->nullable()->after("especifique_negativa");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rel_vehiculos', function($table) {
            $table->dropcolumn('tipo_uso_vehiculo_id');
            $table->dropcolumn('otra_marca');
        });
        Schema::table('rel_victimas_lesionados', function($table) {
            $table->dropcolumn('diagnostico');
        });
    }
}
