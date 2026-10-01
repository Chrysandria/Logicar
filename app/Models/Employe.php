<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    protected $fillable = ["matricule","nom","service_sigle","telephone","lieu_ramassage","pnt_ramassage_dec","pnt_ramassage_adm"];
    public $timestamps = false;
}
