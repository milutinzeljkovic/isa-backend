<?php

namespace App\Services;

use App\Services\IDiagnoseService;
use App\Diagnose;
use Auth;

class DiagnoseService implements IDiagnoseService
{
    public function addDiagnose(array $diagnoseData)
    {
        $currentUser = Auth::user();

        $clinicalCenterAdmin = $currentUser->userable()->get()[0];

        $diagnose = new Diagnose();

        $diagnose->name = array_get($diagnoseData, 'name');
        $diagnose->description = array_get($diagnoseData, 'description');
        $diagnose->clinical_center_id = $clinicalCenterAdmin->clinical_center_id;
        $diagnose->save();
       

        return response()->json(['created' => 'Diagnose has been created'], 201);
    }

}