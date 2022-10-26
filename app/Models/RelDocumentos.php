<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelDocumentos extends Model
{
    protected $fillable = ["nombre", "lesiones_id" ];
    protected $table = "rel_documentos";
}
