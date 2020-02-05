<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }

    public function clinic()
    {
        return $this->belongsTo('App\Clinic');
    }

    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }

    public function recensions()
    {
        return $this->hasMany('App\Recension');
    }

    public function appointmentTypes()
    {
        return $this->belongsToMany('App\AppointmentType')
                ->as('appointment_type_doctor');
    }
    
    public function workingDays()
    {
        return $this->hasMany('App\WorkingDay');
    }

    public function operations()
    {
        return $this->belongsToMany('App\Operations')
                    ->as('operation_doctor');

    }
}
