<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelVehiculos extends Model
{
    use SoftDeletes;
    protected $fillable = ["catalogo_tipo_vehiculo_id", "marca_id", "placa_pais", "no_ocupantes", "color", "con_placas", "entidad_placas", "no_placa", "lesiones_id" ];
    protected $table = "rel_vehiculos";

    public function tipo(){
        return $this->belongsTo('App\Models\Catalogos\TipoVehiculos', 'catalogo_tipo_vehiculo_id', 'id');
    }

    public function marca(){
        return $this->belongsTo('App\Models\Catalogos\Vehiculos', 'marca_id', 'id');
    }
    
    public function estado(){
        return $this->belongsTo('App\Models\Catalogos\Entidades', 'entidad_placas', 'id');
    }
}
