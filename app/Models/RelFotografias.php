<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelFotografias extends Model
{
    protected $fillable = ["nombre_imagen", "lesiones_id" ];
    protected $table = "rel_fotografias";
}
