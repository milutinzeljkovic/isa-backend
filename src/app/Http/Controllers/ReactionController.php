<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recension;
use App\Clinic;
use Auth;

class ReactionController extends Controller
{
    public function store(Request $request, $id)
    {
        $patient = Auth::user()->userable()->first();
        $clinic = Clinic::findOrFail($id);

        $reaction = Recension::updateOrCreate(
            array_merge(
                ['clinic_id' => $request->all()['clinic_id']],
                ['patient_id' => $patient->id]),
                ['stars_count' => $request->all()['stars_count']]
            );
        $clinic = Clinic::find($id);
        $clinicRecensions = $clinic->recensions()->get();
        
        $cnt = 0;
        $recensionSum = 0;
        foreach ($clinicRecensions as $recension) {
            $cnt++;
            $recensionSum += $recension->stars_count;
        }

        $avg = $recensionSum/$cnt;
        $new = round($avg,2);
        $clinic->stars_count = $new;
        $clinic->save();

        return $clinic;
    }
}
