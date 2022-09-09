<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Localidades extends Model
{
    use SoftDeletes;
    protected $fillable = [ 'id', 'description' ];
    protected $table = "catalogo_localidades";
}
