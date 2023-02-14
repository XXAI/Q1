<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelVictimasLesionados extends Model
{
    use SoftDeletes;
    protected $fillable = ["tipo_id", "acta_certificacion_id", "no_acta_certificacion", "nombre", "apellido_paterno", "apellido_materno", "edad", "sexo_id", "tipo_usuario_id", "hospitalizacion", "lesiones_id", "clues", "anonimo","municipio_hospitalizacion", "ubicacion", "pre_hospitalizacion", "embarazada", "prestador_servicio", "otro_prestador", "nivel_conciencia", "pulso", "color_piel", "prioridad_traslado", "negativa_traslado", "especifique_negativa", "no_ambulancia", "especifique_negativa", "vehiculo_id", "diagnostico" ];
    protected $table = "rel_victimas_lesionados";

    public function LesionParte(){
        return $this->hasMany('App\Models\RelLesionParte');
    }

    public function CluesHospitalizacion(){
        return $this->belongsTo('App\Models\Catalogos\Clues', 'clues', 'clues');
    }
}
