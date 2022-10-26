<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelLesionParte extends Model
{
    protected $fillable = ["rel_victimas_lesionados_id", "orientacion", "plano", "parte" ];
    protected $table = "rel_lesion_parte";

    public function lesionVictima(){
        return $this->hasMany('App\Models\RelLesionParteTipo');
    }
}