<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelVictimasLesionados extends Model
{
    use SoftDeletes;
    protected $fillable = ["tipo_id", "acta_certificacion_id", "no_acta_certificacion", "nombre", "apellido_paterno", "apellido_materno", "edad", "sexo_id", "tipo_usuario_id", "hospitalizado", "lesiones_id" ];
    protected $table = "rel_victimas_lesionados";

    public function LesionParte(){
        return $this->hasMany('App\Models\RelLesionParte');
    }
}
