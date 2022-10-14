<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clues extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'id', 'institucion', 'catalogo_municipios_id', 'description' ];
    protected $table = "catalogo_clues";
}
