<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelLesionParteTipo extends Model
{
    protected $fillable = ["rel_lesion_parte_id", "opcion" ];
    protected $table = "rel_lesion_parte_tipo";
}
