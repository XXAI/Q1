<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogoMunicipiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('catalogo_municipios', function (Blueprint $table) {
            $table->smallIncrements('id')->unsigned();
			$table->smallInteger('distrito_id')->unsigned();
            $table->string('clave_municipio', 191);
			$table->string('descripcion', 191);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('catalogo_municipios', function($table) {
            $table->foreign('distrito_id')->references('id')->on('catalogo_distritos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogo_municipios');
    }
}
