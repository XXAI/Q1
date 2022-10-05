<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelTipoAccidente extends Model
{
    protected $fillable = ["rel_tipo_accidente_id", "lesiones_id" ];
    protected $table = "rel_tipo_accidente";
}
