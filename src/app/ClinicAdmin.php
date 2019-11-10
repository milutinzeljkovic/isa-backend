<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClinicAdmin extends Model
{
    public function image()
    {
        return $this->morphOne('App\User', 'userable');
    }
}
