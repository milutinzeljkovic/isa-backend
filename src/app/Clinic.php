<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'name', 'address', 'description'
    ];

    public function patients()
    {
        return $this->hasMany('App\Patient');
    }

    public function doctors()
    {
        return $this->hasMany('App\Doctor');
    }

    public function nurses()
    {
        return $this->hasMany('App\Nurse');
    }

    public function clinicAdmins()
    {
        return $this->hasMany('App\ClinicAdmin');
    }

    public function operationRooms()
    {
        return $this->hasMany('App\OperationsRoom');
    }

    public function appointmentTypes()
    {
        return $this->belongsToMany('App\AppointmentType')
                ->as('clinics_appointment_types');
    }

    public function appointments()
    {
        return $this->hasMany('App/Appointment');
    }
}
