<?php

namespace App\Services;

use App\Services\IDiagnoseService;
use App\Diagnose;
use Auth;
use DB;

class DiagnoseService implements IDiagnoseService
{
    public function addDiagnose(array $diagnoseData)
    {
       

        $diagnose = new Diagnose();

        $diagnose->name = array_get($diagnoseData, 'name');
        $diagnose->label = array_get($diagnoseData, 'label');
        $diagnose->save();
       

        return response()->json(['created' => 'Diagnose has been created'], 201);
    }

    public function getDiagnoses()
    {
        $diagnoses = DB::table('diagnoses')->get();

        return $diagnoses;
    }

}