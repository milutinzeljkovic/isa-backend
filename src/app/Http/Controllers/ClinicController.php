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

    public function show($id)
    {
        return $this->_clinicService->showClinic($id);
    }

    public function index(Request $request)
    {   
        return $this->_clinicService->searchClinic($request->input('name'),$request->input('date'),$request->input('stars'), $request->input('address'), $request->input('appointment_type'));
    }

    public function store(ClinicStoreRequest $request)
    {
        return $this->_clinicService->addClinic($request->validated());
    }
    
    public function doctors(Clinic $clinic, Request $request)
    {
       
        $result = $clinic->doctors()->with('user')->with(['appointments' => function ($q) {
            $q->where('patient_id','=',null)
              ->where('date', '>', Carbon::createFromTimestamp(Carbon::now()->getTimestamp())->toDateTimeString())
                ->with('operationsRoom')
                ->with('appointmentType')
                ->with('doctor.user');
        }])
        ->get();

        return $result;
    }
}
