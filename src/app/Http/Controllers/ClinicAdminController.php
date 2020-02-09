<?php

namespace App\Http\Controllers;

use App\ClinicAdmin;
use Illuminate\Http\Request;
use Auth;
use App\Services\ClinicAdminService;

class ClinicAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $_clinicAdminService;

    public function __construct(ClinicAdminService $_clinicAdminService)
    {
        $this->_clinicAdminService = $_clinicAdminService;
    }

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
     * @param  \App\ClinicAdmin  $clinicAdmin
     * @return \Illuminate\Http\Response
     */
    public function show(ClinicAdmin $clinicAdmin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ClinicAdmin  $clinicAdmin
     * @return \Illuminate\Http\Response
     */
    public function edit(ClinicAdmin $clinicAdmin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ClinicAdmin  $clinicAdmin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClinicAdmin $clinicAdmin)
    {
        //
    }

    public function getAllDoctors()
    {
        return $this->_clinicAdminService->getAllDoctors();
    }

    public function getAllFacilities()
    {
        return $this->_clinicAdminService->getAllFacilities();
    }

    public function getAdminsClinic()
    {
        return $this->_clinicAdminService->getAdminsClinic();
    }

    public function getOperations()
    {
        return $this->_clinicAdminService->getOperations();
    }

    public function updateClinic(Request $request)
    {
        $credentials = $request->only('name', 'description', 'id', 'clinic_center', 'address');
        return $this->_clinicAdminService->updateClinic($credentials);
    }

    public function changeDateoOfOperation(Request $request)
    {
        $credentials = $request->only('room_id', 'operation_id', 'date');
        return $this->_clinicAdminService->changeDateoOfOperation($credentials);
    }

    public function editOperation(Request $request)
    {
        $credentials = $request->only('doctors', 'operation_id');
        return $this->_clinicAdminService->editOperation($credentials);
    }

    public function addDuration(Request $request)
    {
        $credentials = $request->only('duration', 'operation_id');
        return $this->_clinicAdminService->addDuration($credentials);
    }

    public function clinicDoctors()
    {
        $user = Auth::user();
        $admin = $user->userable()->first();
        $clinic = $admin->clinic()->first();
        return $clinic->doctors()->with('user')->get();
    }

    public function reserveOperation(Request $request)
    {
        return $this->_clinicAdminService->reserveOperation($request->input('operations_room_id'),$request->input('operation_id'));
    }

    public function reserveAppointmentRoom(Request $request)
    {
        return $this->_clinicAdminService->reserveAppointmentRoom($request->input('operations_room_id'),$request->input('appointment_id'));

    }

    public function pendingAppointmentRequests()
    {
        return $this->_clinicAdminService->pendingAppointmentRequests();

    }

    public function getByAppType($id){
        return $this->_clinicAdminService->getByAppType($id);
    }

    public function specializeDoctor(Request $request, $id){
        $data = $request->all();
        return $this->_clinicAdminService->specializeDoctor($id, $data);
    }

    public function updateAppointmentRequest(Request $request){
        $data = $request->all();
        return $this->_clinicAdminService->updateAppointmentRequest($data);
    }

    public function getAverageClinicRating(){
        return $this->_clinicAdminService->getAverageClinicRating();
    }

    public function getAverageRatingDoctor(){
        return $this->_clinicAdminService->getAverageRatingDoctor();
    }

    public function earnedMoneyInPeriod(Request $request){
        $data = $request->all();
        return $this->_clinicAdminService->earnedMoneyInPeriod($data);
    }

    public function weeklyNumberOfAppointments(){
        return $this->_clinicAdminService->weeklyNumberOfAppointments();
    }

    public function monthlyNumberOfAppointments(){
        return $this->_clinicAdminService->monthlyNumberOfAppointments();
    }
}
