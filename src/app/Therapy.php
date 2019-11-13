<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Therapy extends Model
{
    public function medicalReport()
    {
        return $this->belongsTo('App\MedicalReport');
    }

    public function medicines()
    {
        return $this->belongsToMany('App\Medicine')
                ->as('medicine_therapy')
                ->withPivot('data');
    }
}
