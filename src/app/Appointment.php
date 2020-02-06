<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Reshadman\OptimisticLocking\OptimisticLocking;

class Appointment extends Model
{

    //use OptimisticLocking;

    protected $fillable = [
        'date',
        'price',
        'done',
        'lock_version',
        'operations_room_id',
        'duration',
        'discount',
        'appointment_type_id',
        'doctor_id',
        'clinic_id'
    ];

    protected $guarded = [
        'approved'
    ];

    public function appointmentType()
    {
        return $this->belongsTo('App\AppointmentType');
    }
    
    public function clinic()
    {
        return $this->belongsTo('App\Clinic');
    }

    public function operationsRoom()
    {
        return $this->belongsTo('App\OperationsRoom');
    }

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }

    public function medicalReport()
    {
        return $this->hasOne('App\MedicalReport');

    }
}
