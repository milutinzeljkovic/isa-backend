<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recension extends Model
{
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'stars_count',
        'doctor_id'
    ];

    public function clinic()
    {
        return $this->belongsTo('App\Clinic');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }
}
