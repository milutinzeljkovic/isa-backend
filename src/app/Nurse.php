<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nurse extends Model
{
    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }

    public function clinic()
    {
        return $this->belongsTo('App\Clinic');
    }

    public function businessHours()
    {
        return $this->hasMany('App\BusinessHours');
    }
}
