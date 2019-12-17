<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clinic;
use App\Http\Requests\ClinicStoreRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Services\IClinicService;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class ClinicController extends Controller
{
    private $_clinicService;

    public function __construct(IClinicService $clinicService)
    {
        $this->_clinicService = $clinicService;
    }

    public function index(Request $request)
    {   
        return $this->_clinicService->searchClinic($request->input('name'));
    }

    public function store(ClinicStoreRequest $request)
    {
        return $this->_clinicService->addClinic($request->validated());
    }
    
    public function doctors(Clinic $clinic)
    {
      /*  $doctors = $clinic->doctors()->with(['user', 'appointments', 'appointments.operationsRoom'])->get();
        return $doctors;

       /* $a=array();
        $doctors = $clinic->doctors()->get();
        $collection = collect($doctors);
        foreach ($doctors as $doctor) {
            array_push($a,$doctor->with('user')->get()[0]);
        }
        return $a;
        */
        $result = $clinic->doctors()->with('user')->with(['appointments' => function ($q) {
            $q->where('patient_id','=',null);
                 
        }])
        ->get();

        /*
        $users = DB::table('doctors')
                    ->join('users','doctors.id','=','users.userable_id')
                    ->where('users.userable_type', '=', 'App\Doctor')
                    ->where('doctors.clinic_id','=',$clinic->id)
                    ->join('appointments','appointments.doctor_id', '=', 'doctors.id')
                    ->get();
        return $users;*/
        return $result;
    }
}
