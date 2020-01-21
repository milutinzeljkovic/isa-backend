<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IDoctorService;
use App\Appointment;
use App\Doctor;
use App\User;


class DoctorController extends Controller
{
    private $_doctorService;

    public function __construct(IDoctorService $doctorService)
    {
        $this->_doctorService = $doctorService;
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->_doctorService->showDoctor($id);
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
        $user = User::find($id);
        $doctor = Doctor::where('id', $user->userable_id)->get()[0];

        $doctor->update($values);
        return response()->json(['message' => 'Doctor successfully updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $doctor = Doctor::where('id', $user->userable_id)->get()[0];

        $allApps = Appointment::all();

        foreach($allApps as $appointment){
            if($appointment->doctor_id == $doctor->id){    //za sad ne proverava da li je termin zakazan
                return response()->json(['message' => "The doctor you are trying to delete has an appointment booked"], 400);
            }
        }

        $doctor->delete();
        return response()->json(['message' => 'Doctor successfully deleted'], 200);
    }
}
