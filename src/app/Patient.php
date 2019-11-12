<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }

    public function clinics()
    {
        return $this->belongsToMany('App\Clinic')
                ->as('clinic_patient');
    }
    
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }

    public function medicalRecord()
    {
        return $this->hasOne('App\MedicalRecord');
    }
}
