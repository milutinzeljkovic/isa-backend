<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }

    public function nurse()
    {
        return $this->belongsTo('App\Nurse');
    }

    protected $fillable = [
        'day',
        'from',
        'to',
        'doctor_id'
    ];
}
