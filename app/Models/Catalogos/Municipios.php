<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipios extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'id', 'description' ];
    protected $table = "catalogo_municipios";
}
