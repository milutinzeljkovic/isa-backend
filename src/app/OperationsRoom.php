<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationsRoom extends Model
{
    protected $fillable = [
        'name', 'number'
    ];

    public function clinic()
    {
        return $this->belongsTo('App\Clinic');
    }
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }

}
