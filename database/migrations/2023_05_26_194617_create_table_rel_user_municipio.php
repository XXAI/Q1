<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRelUserMunicipio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rel_user_municipio', function (Blueprint $table) {
            $table->smallInteger("user_id");
            $table->smallInteger("catalogo_municipio_id");
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('users', function($table)
        {
            $table->dropcolumn('catalogo_municipio_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rel_user_municipio');
        Schema::table('users', function($table)
        {
            $table->smallInteger('catalogo_municipio_id')->unsigned()->nullable()->after("avatar");
        });
    }
}
