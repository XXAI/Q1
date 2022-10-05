<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelCausaPasajero extends Model
{
    protected $fillable = ["causa_pasajero", "lesiones_id" ];
    protected $table = "rel_causa_pasajero";
}
