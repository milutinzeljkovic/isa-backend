<?php

namespace App\Http\Controllers;
use App\Services\IVacationService;
use App\Vacation;
use App\User;
use App\Mail\DeclineMail;

use Illuminate\Http\Request;

class VacationController extends Controller
{
    private $_vacationService;

    public function __construct(IVacationService $vacationService)
    {
        $this->_vacationService = $vacationService;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getVacationRequests()
    {
        return $this->_vacationService->getVacationRequests();
    }

    public function approveVacationRequest($id)
    {
        return $this->_vacationService->approveVacationRequest($id);
    }

    public function declineVacationRequest(Request $request, $id)
    {
        $vacation = Vacation::where('id', $id)->get()[0];
        $user = User::where('id', $vacation->user_id)->get()[0];
        $msgs = $request->query('msg');
        \Mail::to($user)->send(new DeclineMail($user, $msgs));
        $vacation->approved = 0;
        $vacation->save();

        return $vacation->id;
    }
}
