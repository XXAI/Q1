<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelCausaConductorDetalles extends Model
{
    protected $fillable = ["sexo_id", "alcoholico", "cinturon", "edad", "ignora_edad", "lesiones_id" ];
    protected $table = "rel_causa_conductor_detalles";
}
