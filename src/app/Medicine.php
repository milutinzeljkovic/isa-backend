<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = ['name','label'];

    //kom klinickom centru pripada taj lek
    public function clinicalCenter()
    {
        return $this->belongsTo('App\ClinicalCenter');
    }

    public function therapies()
    {
        return $this->belongsToMany('App\Therapy')
                ->as('medicine_therapy')
                ->withPivot('data');
    }
}
