<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelFallaVehiculo extends Model
{
    protected $fillable = ["rel_falla_vehiculo_id", "lesiones_id" ];
    protected $table = "rel_falla_vehiculo";
}
