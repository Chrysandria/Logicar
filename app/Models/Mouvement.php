<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = ["date","mtrl_car","distance_parcourue"];
}
