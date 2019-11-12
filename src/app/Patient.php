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
                ->as('patient_clinic_type');
    }
    
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }
}
