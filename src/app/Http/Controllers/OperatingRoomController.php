<?php

namespace App\Http\Controllers;
use App\Services\OperatingRoomService;
use App\Http\Requests\OperationRoomRequest;
use App\Appointment;
use App\OperationsRoom;

use Illuminate\Http\Request;

class OperatingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(OperatingRoomService $operatingRoomService)
    {
        $this->_operatingRoomService = $operatingRoomService;
    }

    public function index(Request $request)
    {
        return $this->_operatingRoomService->searchOperatingRooms($request->input('name'),$request->input('number'),$request->input('date'));
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
    public function store(OperationRoomRequest $request)
    {
        return $this->_operatingRoomService->addOperatingRoom($request->validated());
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

        $opRoom = OperationsRoom::find($id);
        $opRoom->update($values);
        return response()->json(['message' => "Operating room successfully updated"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $opRoom = OperationsRoom::find($id);
        $opRoom->delete();
        return $opRoom->id;
    }

    public function getOpRooms(){
        return $this->_operatingRoomService->getOperatingRooms();
    }

    public function seeIfOpRoomBooked($id)
    {
        return $this->_operatingRoomService->seeIfOpRoomBooked($id);
    }

    public function getAppointments($id)
    {
        return $this->_operatingRoomService->getAppointments($id);
    }

    public function getFirstFreeDate($id)
    {
        return $this->_operatingRoomService->getFirstFreeDate($id);
    }
}
