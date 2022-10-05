<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelCondicionCamino extends Model
{
    protected $fillable = ["rel_condicion_camino_id", "lesiones_id" ];
    protected $table = "rel_condicion_camino";
}
