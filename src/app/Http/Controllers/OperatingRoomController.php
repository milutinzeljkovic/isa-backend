<?php

namespace App\Http\Controllers;
use App\Services\OperatingRoomService;
use App\Http\Requests\OperationRoomRequest;
use App\Appointment;
use App\OperationsRoom;
use App\Operations;
use Carbon\Carbon;
use App\Mail\AddOperationRoomMail;



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

    public function getOperations($id)
    {
        return $this->_operatingRoomService->getOperations($id);
    }

    public function getFirstFreeDate($id)
    {
        return $this->_operatingRoomService->getFirstFreeDate($id);
    }

    public function handle()
    {
        $operation_rooms = OperationsRoom::all();

        $operations = Operations::where('operations_rooms_id',null)
                                ->where('date','>',Carbon::now())
                                ->get();

        foreach($operations as $o){
            $dates = array();

            foreach($operation_rooms as $oproom){
                $start = explode(' ',Carbon::parse($o->date))[0];

                $datum= $this->_operatingRoomService->getFirstFreeDateFromDate($oproom->id,$start,$o->clinic_id);
                $year = (int)explode('-', $datum)[0];
                $month = (int)explode('-', $datum)[1];
                $day = (int)explode('-', $datum)[2];

                if($month < 10){
                    if($day < 10){
                        $formattedDate = $year.'-0'.$month.'-0'.$day;
                    }else {
                        $formattedDate = $year.'-0'.$month.'-'.$day;
                    }
                }else {
                    if($day < 10){
                        $formattedDate = $year.'-'.$month.'-0'.$day;
                    }else {
                        $formattedDate = $year.'-'.$month.'-'.$day;
                    }
                }

                array_push($dates, $formattedDate.' '.$oproom->id);

            }

            $min=$dates[0];
            foreach($dates as $d){
                if(explode(' ',$d)[0] < explode(' ',$min)[0]){
                    $min=$d;
                }

            }

            $o->operations_rooms_id=(int)explode(' ',$min)[1];
            $time = explode(' ',Carbon::parse($o->date))[1];
            $o->date=explode(' ',$min)[0].' '.$time;
            $o->save();

            $pat=$o->patient()->first();
            $user=$pat->user()->first();

            $rom = OperationsRoom::where('id',(int)explode(' ',$min)[1])->first();

            \Mail::to($user)->send(new AddOperationRoomMail($user, $o,$rom));






        }
    }
}
