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
        'done'
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
        return $this->hasOne('App\OperationsRoom');
    }

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }
}
