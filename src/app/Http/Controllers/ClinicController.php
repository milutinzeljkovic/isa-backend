<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ClinicStoreRequest;

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
    
}
