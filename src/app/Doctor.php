<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }

    public function clinic()
    {
        return $this->hasOne('App\Clinic');
    }
}
