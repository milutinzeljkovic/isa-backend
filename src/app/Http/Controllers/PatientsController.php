<?php

namespace App\Http\Controllers;
use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserUpdateRequest;
use App\Mail\ActivateMail;
use App\Mail\DeclineMail;
use App\Services\IPatientService;

use Illuminate\Support\Facades\Crypt;



class PatientsController extends Controller
{
    //
    private $_patientService;

    public function __construct(IPatientService $patientService)
    {
        $this->_patientService = $patientService;
    }

    public function view($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    public function update(Request $request,  $id)
    {
        $user = User::findOrFail($id);
        $values = $request->all();
        $user->update($values);
        return $user;
    }


    public function accept($id)
    {
        $user = User::findOrFail($id);
        $encrypted = Crypt::encryptString($id);

        \Mail::to($user)->send(new ActivateMail($user,$encrypted));
        $user->confirmed = 1;
        $user->save();
        
        return response()->json(['message' => 'Registration accepted']);
    }

    public function decline(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $msgs = $request->query('msg');
        \Mail::to($user)->send(new DeclineMail($user, $msgs));
        $user->confirmed = -1;
        $user->save();
        
        return response()->json(['message' => 'Registration declined']);
    }

    function getPatients(){
        $patients = User::where('userable_type',"App\Patient")->where('confirmed',0)->get();
        return $patients;
    }

    function getClinicsPatients(){
        return $this->_patientService->getPatientsByClinic();
    }

}
