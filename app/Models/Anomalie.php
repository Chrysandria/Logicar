<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anomalie extends Model
{
    protected $fillable = ["mtrl_car","description","debut","fin"];
}
