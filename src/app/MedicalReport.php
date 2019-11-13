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
    
}
