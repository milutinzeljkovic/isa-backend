<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IDoctorService;
use App\Appointment;
use App\Doctor;
use App\User;
use App\WorkingDay;
use App\Vacation;


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
    public function index(Request $request)
    {  
        return $this->_doctorService->searchDoctors($request->input('clinic_id'),$request->input('name'),$request->input('date'), $request->input('stars'), $request->input('appointment_type'));
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

    public function showDoctorAppointments($id)
    {
        return $this->_doctorService->showDoctorsAppointments($id);
    }

    public function getApointments()
    {
        return $this->_doctorService->getApointments();
    }

    public function getDataForDoctor($appointment_id)
    {
        return $this->_doctorService->getDataForDoctor($appointment_id);
    }

    public function medicalReportForAppointment(Request $request)
    {

        $credentials = $request->only('height', 'weight', 'allergy', 'diopter', 'blood_type', 'therapy', 'diagnose', 'medicines', 'appointment_id');

        return $this->_doctorService->medicalReportForAppointment($credentials);
    }

    public function sheduleAnOperation(Request $request)
    {

        $credentials = $request->only('appointment_id', 'date', 'info');

        return $this->_doctorService->sheduleAnOperation($credentials);
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

        $user->update($values);
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

        //obrisati mu radne sate
        $workingDays = WorkingDay::where('doctor_id', $doctor->id)->get();
        foreach ($workingDays as $workingDay) {
            $workingDay->delete();
        }

        $vacations = Vacation::where('user_id', $id)->get();
        
        //ukoliko je imao neke zahteve za odmor ili ceka na odobrenje da se obrisu
        if(count($vacations) > 0){
            foreach($vacations as $vacation){
                $vacation->delete();
            }
        }

        $doctor->user()->delete();
        $doctor->delete();

        
        return $doctor->id;
    }

    public function seeIfDoctorUsed($id)
    {
        return $this->_doctorService->seeIfDoctorUsed($id);
    }

    public function getWorkingHours($id){
        return $this->_doctorService->getWorkingHours($id);
    }
}
