<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalData extends Model
{
    protected $fillable = [
        'name',
        'unit'

    ];

    public function medicalRecords()
    {
        return $this->belongsToMany('App\MedicalRecord')
                ->as('medical_data_medical_record')
                ->withPivot('value');
    }
}
