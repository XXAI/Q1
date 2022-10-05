<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelCausaConductor extends Model
{
    protected $fillable = ["rel_causa_conductor_id", "lesiones_id" ];
    protected $table = "rel_causa_conductor";
}
