<?php

namespace App\Services;

use App\Services\IClinicAdminService;
use Auth;
use App\Clinic;
use App\User;
use App\Doctor;
use App\Operations;

class ClinicAdminService implements IClinicAdminService
{
    function getAllDoctors(){

        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $doctors = Doctor::where('clinic_id', $clinicAdmin->clinic_id)->with('user')->get();

        return $doctors;
    }

    function getAllFacilities(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        $facilities = $clinic->operationRooms;

        return $facilities;
    }

    function getOperations(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $operations = Operations::where('clinic_id', $clinicAdmin->clinic_id)
                        ->where('operations_rooms_id',null)
                        ->with('doctors')
                        ->with(['patient' => function($q) {
                            $q->with('user');
                        }])->get();


        return $operations;



    }

    function editOperation(array $userData){


        $doctors = array_get($userData, 'doctors');
        $duration = array_get($userData, 'duration');
        $operation_id = array_get($userData, 'operation_id');

        $operation = Operations::where('id', $operation_id)->first();

        $operation->duration=$duration;
        $operation->save();

        foreach($doctors as $d){

            $doctor= Doctor::where('id',$d['id'])->first();
            $operation->doctors()->attach($doctor);
        }


        return response()->json(['updated' => 'Operation has been updated'], 201);


    }



    function getAdminsClinic()
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        return $clinic;
    }

    function updateClinic(array $newClinicData)
    {
        $clinic = Clinic::where('id', array_get($newClinicData, 'id'))->get()[0];

        $clinic->name = array_get($newClinicData, 'name');
        $clinic->description = array_get($newClinicData, 'description');
        $clinic->clinical_center_id = array_get($newClinicData, 'clinic_center');
        $clinic->address = array_get($newClinicData, 'address');

        $clinic->save();

        return response()->json(['updated' => 'Clinic has been updated'], 201);
    }
}