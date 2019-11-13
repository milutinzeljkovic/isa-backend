<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClinicalCenter extends Model
{
    public function clinicalCenterAdmins()
    {
        return $this->hasMany('App\ClinicalCenterAdmin');
    }

    public function medicines()
    {
        return $this->hasMany('App\Medicine');
    }

    public function diagnoses()
    {
        return $this->hasMany('App\Diagnose');
    }

    public function clinics()
    {
        return $this->hasMany('App\Clinic');
    }
}
