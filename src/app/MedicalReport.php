<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalReport extends Model
{
    public function medicalRecord()
    {
        return $this->belongsTo('App\MedicalRecord');
    }

    public function diagnose()
    {
        return $this->belongsTo('App\Diagnose');
    }
    
}
