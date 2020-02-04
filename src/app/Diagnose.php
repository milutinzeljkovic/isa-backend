<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Diagnose extends Model
{
    protected $fillable = 
    [
        'name',
        'label'
    ];

    public function medicalReports()
    {
        return $this->hasMany('App\MedicalReport');
    }

    public function clinicalCenter()
    {
        return $this->belongsTo('App\ClinicalCenter');
    }
}
