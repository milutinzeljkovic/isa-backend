<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Clinic;
use App\Http\Requests\ClinicStoreRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Services\IClinicService;


class ClinicController extends Controller
{
    private $_clinicService;

    public function __construct(IClinicService $clinicService)
    {
        $this->_clinicService = $clinicService;
    }

    public function index(Request $request)
    {
        return $this->_clinicService->searchClinic();
    }

    public function store(ClinicStoreRequest $request)
    {
        return $this->_clinicService->addClinic($request->validated());
    }
    
    public function doctors(Clinic $clinic)
    {
        $a=array();
        $doctors = $clinic->doctors()->get();
        $collection = collect($doctors);
        foreach ($doctors as $doctor) {
            array_push($a,$doctor->user()->get()[0]);

        }
        return $a;
    }
}
