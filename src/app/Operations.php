<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operations extends Model
{
     protected $fillable = [
        'date',
        'lock_version',
        'operations_room_id',
        'duration',
        'clinic_id',
        'patient_id'
    ];


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

    public function nurses()
    {
        return $this->belongsToMany('App\Nurse')
                    ->as('operation_nurse');
    }

    public function doctors()
    {
        return $this->belongsToMany('App\Doctor')
                    ->as('operation_doctor');
    }


}
