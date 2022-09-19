<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesiones extends Model
{
    use SoftDeletes;
    protected $fillable = [ ];
    protected $table = "lesiones";

    public function municipio(){
        return $this->belongsTo('App\Models\Catalogos\Municipios', 'municipio_id', 'id');
    }
}
