<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Prescription extends Model
{

    protected $fillable = [
        'info',
        'nurse_id'
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
