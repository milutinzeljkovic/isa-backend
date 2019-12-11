<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{

    protected $fillable = [
        'info'
    ];

    public function medicalReport()
    {
        return $this->belongsTo('App\MedicalReport');
    }

    public function medicine()
    {
        return $this->belongsTo('App\Medicine');
    }

    public function nurse()
    {
        return $this->belongsTo('App\Nurse');
    }

}
