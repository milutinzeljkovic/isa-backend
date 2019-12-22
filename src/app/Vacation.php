<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    protected $fillable = [
        'from', 'to' ];

    protected $guarded = [
        'approved'
    ];
    public function users()
    {
        return $this->belongsTo('App\User');

    }

}
