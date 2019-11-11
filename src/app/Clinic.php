<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'name', 'address', 'description'
    ];

    public function patients()
    {
        return $this->hasMany('App\Patient');
    }

    public function doctors()
    {
        return $this->hasMany('App\Doctor');
    }

    public function nurses()
    {
        return $this->hasMany('App\Nurse');
    }

    public function clinicAdmins()
    {
        return $this->hasMany('App\ClinicAdmin');
    }

    
}
