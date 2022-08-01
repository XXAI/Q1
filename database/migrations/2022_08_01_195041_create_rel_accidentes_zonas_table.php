<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelAccidentesZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_accidentes_zonas', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tipo_zona_id')->unsigned();
            $table->boolean('esCarreteraEstatal');
            $table->string('calle_uno')->nullable();
            $table->string('calle_dos')->nullable();
            $table->string('referencia')->nullable();
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
        Schema::dropIfExists('rel_accidente_zona');
    }
}
