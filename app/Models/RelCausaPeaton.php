<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelCausaPeaton extends Model
{
    protected $fillable = ["rel_causa_peaton_id", "lesiones_id" ];
    protected $table = "rel_causa_peaton";
}
