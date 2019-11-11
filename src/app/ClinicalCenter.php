<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClinicalCenter extends Model
{
    public function clinicalCenterAdmins()
    {
        return $this->hasMany('App\ClinicalCenterAdmin');
    }
}
