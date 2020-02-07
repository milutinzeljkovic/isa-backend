<?php

namespace App\Services;

use App\Services\IAppointmentTypeService;
use App\AppointmentType;
use App\Clinic;
use Auth;
use App\Appointment;
use App\User;
use App\Doctor;
use App\Utils\SimpleFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Price;

class AppointmentTypeService implements IAppointmentTypeService
{
    public function addAppointmentType(array $appTypeData)
    {
        $factory = new SimpleFactory();
        $appType = $factory->createAppointmentType();

        $appType->name = array_get($appTypeData, 'name');
        $user = Auth::user()->userable()->first();

        $clinic = Clinic::find($user->clinic_id);
        $clinic->appointmentTypes()->save($appType, ['price' => array_get($appTypeData, 'price') ]);

        $price = new Price();
        $price->clinic_id = $user->clinic_id;
        $price->price = array_get($appTypeData, 'price');

        $appType->save();
        $price->appointment_type_id = $appType->id;
        $price->save();         //////////////////////ZEKU PITAJ
       

        return response()->json(['created' => 'Appointment type has been created'], 201);
    }

    public function getAppointmentTypes()
    {
        $appointments = AppointmentType::all();
        return $appointments;
    }

    public function appointmentTypesClinic()
    {
        $user = Auth::user();
        $admin = $user->userable()->first();
        $clinic = $admin->clinic()->first();
        return $clinic->appointmentTypes()->get();
    }

    public function seeIfAppTypeUsed($id)
    {
        $allApps = Appointment::whereDate('date', Carbon::now())->where('patient_id', '!=', null)->where('operations_room_id', '!=', null)->get(); //ne brisemo preglede koji ce se tek desiti i vec su zakazani

        foreach($allApps as $appointment){
            if($appointment->appointment_type_id == $id){    
                return response()->json(["true"], 200);
            }
        }

        return response()->json(["false"], 200);
    }

    public function getDoctorsOptions($id){
        $user = User::where('id', $id)->first();
        $appTypes = DB::table('appointment_type_doctor')->where('doctor_id', $user->userable_id)->get();

        $allAppTypes = AppointmentType::all();
        $retVal = collect();
        foreach($allAppTypes as $appT){
            if(!$appTypes->contains('appointment_type_id', $appT->id)){
                $retVal->push($appT);
            }
        }

        return $retVal;
    }
}