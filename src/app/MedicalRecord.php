<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public function medicalDatas()
    {
        return $this->belongsToMany('App\MedicalData')
                ->as('medical_data_medical_record')
                ->withPivot('value');
    }

    public function medicalReports()
    {
        return $this->hasMany('App\MedicalReport');
    }
}
