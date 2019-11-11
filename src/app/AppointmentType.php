<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }

    public function clinics()
    {
        return $this->belongsToMany('App\Clinic')
                ->as('clinics_appointment_types');
    }
}
