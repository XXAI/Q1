<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesiones extends Model
{
    use SoftDeletes;
    protected $fillable = [ ];
    protected $table = "lesiones";

    public function municipio(){
        return $this->belongsTo('App\Models\Catalogos\Municipios', 'municipio_id', 'id');
    }
    
    public function localidad(){
        return $this->belongsTo('App\Models\Catalogos\Localidades', 'localidad_id', 'id');
    }

    public function tipoAccidente(){
        return $this->hasMany('App\Models\RelTipoAccidente');
    }

    public function vehiculo(){
        return $this->hasMany('App\Models\RelVehiculos');
    }
    
    public function victima(){
        return $this->hasMany('App\Models\RelVictimasLesionados');
    }

    public function causaAccidente(){
        return $this->hasMany('App\Models\RelCausaAccidente');
    }

    public function causaConductor(){
        return $this->hasMany('App\Models\RelCausaConductor');
    }

    public function causaConductorDetalle(){
        return $this->hasMany('App\Models\RelCausaConductorDetalles');
    }

    public function causaPeaton(){
        return $this->hasMany('App\Models\RelCausaPeaton');
    }

    public function condicionCamino(){
        return $this->hasMany('App\Models\RelCondicionCamino');
    }
    
    public function fallaVehiculo(){
        return $this->hasMany('App\Models\RelFallaVehiculo');
    }

    public function agentes(){
        return $this->hasMany('App\Models\RelAgente');
    }
    
    public function causaPasajero(){
        return $this->hasOne('App\Models\RelCausaPasajero');
    }
}

