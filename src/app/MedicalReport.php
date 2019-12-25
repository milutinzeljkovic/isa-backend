<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalReport extends Model
{

    protected $fillable = [ 'information' ];

    public function medicalRecord()
    {
        return $this->belongsTo('App\MedicalRecord');
    }

    public function diagnose()
    {
        return $this->belongsTo('App\Diagnose');
    }

    public function therapy()
    {
        return $this->hasOne('App\Therapy');
    }

    public function prescriptions()
    {
        return $this->hasMany('App\Prescription');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }
    public function appointment()
    {
        return $this->belongsTo('App\Appointment');
    }
    
}
