<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Diagnose extends Model
{
    protected $fillable = 
    [
        'name',
        'description'
    ];

    public function medicalReports()
    {
        return $this->hasMany('App\MedicalReport');
    }
}
