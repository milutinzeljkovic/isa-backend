<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClinicalCenterAdmin extends Model
{
    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }

    public function clinicalCenter()
    {
        return $this->belongsTo('App\ClinicalCenter');
    }
}
