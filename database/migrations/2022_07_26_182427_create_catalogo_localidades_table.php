<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogoLocalidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('catalogo_localidades', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->index();
            $table->smallInteger('municipio_id')->unsigned();
            $table->string('clave_localidad', 191);
			$table->string('descripcion', 191)->index();
			$table->float('latitud', 15, 0);
			$table->float('longitud', 15, 0);
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('catalogo_localidades', function($table) {
            $table->foreign('municipio_id')->references('id')->on('catalogo_municipios');
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
    }
}
