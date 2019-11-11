<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }

    public function clinic()
    {
        return $this->belongsTo('App\Clinic');
    }
    
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }
}
