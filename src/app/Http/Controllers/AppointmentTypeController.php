<?php

namespace App\Http\Controllers;
use App\Services\AppointmentTypeService;
use App\Http\Requests\AppointmentTypeRequest;
use Illuminate\Http\Request;
use App\AppointmentType;
use App\Appointment;
use Illuminate\Support\Facades\DB;

use App\ClinicAdmin;
use App\Price;
use Auth;

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
        $name = array_get($values, 'name');

        DB::transaction(function () use($name, $id)
        {
            $at =  DB::table('appointment_types')
                ->where('id', $id)
                ->first();
            DB::table('appointment_types')
                ->where('id', $id)
                ->where('lock_version', $at->lock_version)
                ->update([
                        'name' => $name,
                        'lock_version' => $at->lock_version +1
                    ]);            
        });

        $updatedAt = AppointmentType::find($id);

        if($updatedAt->name != $name)
        {
            return response('Error '.json_encode($updatedClinic),400);
        }
        else
        {
            return response()->json(['message' => "Appointment type successfully updated"], 200);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $cA = ClinicAdmin::where('id', $user->userable_id)->first();

        $appType = AppointmentType::find($id);
        $appType->delete();

        $appTypDoctor = DB::table('appointment_type_doctor')->where('appointment_type_id', $id)->get();
        foreach($appTypDoctor as $atd){
            $atd->delete();
        }
        
        $appTypDoctor1 = DB::table('appointment_type_clinic')->where('appointment_type_id', $id)->where('clinic_id', $cA->clinic_id)->delete();

        $appsTyp = Price::where('appointment_type_id', $id)->where('clinic_id', $cA->clinic_id)->delete();

        return $appType->id;
    }

    public function clinicAppointmentTypes()
    {
        return $this->_appointmentTypeService->appointmentTypesClinic();
    }

    public function getAllAppTypes()
    {
        return $this->_appointmentTypeService->getAppointmentTypes();
    }

    public function seeIfAppTypeUsed($id)
    {   
        return $this->_appointmentTypeService->seeIfAppTypeUsed($id);
    }

    public function getDoctorsOptions($id)
    {
        return $this->_appointmentTypeService->getDoctorsOptions($id);
    }
}
