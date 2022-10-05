<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelAgente extends Model
{
    protected $fillable = ["rel_agente_natural_id" ];
    protected $table = "rel_agente_natural";
}
