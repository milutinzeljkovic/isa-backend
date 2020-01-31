<?php

namespace App\Http\Controllers;
use App\Services\AppointmentTypeService;
use App\Http\Requests\AppointmentTypeRequest;
use Illuminate\Http\Request;
use App\AppointmentType;
use App\Appointment;

class AppointmentTypeController extends Controller
{

    public function __construct(AppointmentTypeService $appointmentTypeService)
    {
        $this->_appointmentTypeService = $appointmentTypeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppointmentTypeRequest $request)
    {
        return $this->_appointmentTypeService->addAppointmentType($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $values = $request->all();

        $appType = AppointmentType::find($id);
        $appType->update($values);
        
        return response()->json(['message' => "Appointment type successfully updated"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $appType = AppointmentType::find($id);
        $appType->delete();

        return $appType->id;
    }

    public function getAllAppTypes()
    {
        return $this->_appointmentTypeService->getAppointmentTypes();
    }

    public function seeIfAppTypeUsed($id)
    {   
        return $this->_appointmentTypeService->seeIfAppTypeUsed($id);
    }
}
