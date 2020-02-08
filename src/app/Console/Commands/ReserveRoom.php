<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\OperationsRoom;
use App\Operations;
use Carbon\Carbon;
use App\Mail\AddOperationRoomMail;
use App\Mail\ActivateMail;
use App\Services\OperatingRoomService;



class ReserveRoom extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reserverooms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        parent::__construct();


    }

    

    /**
     * Execute the console command.
     *    
     * @return mixed
     */
    public function handle()
    {
        $x=new OperatingRoomService();
        $operation_rooms = OperationsRoom::all();

        $operations = Operations::where('operations_rooms_id',null)
                                ->where('date','>',Carbon::now())
                                ->get();

        foreach($operations as $o){
            $dates = array();

            foreach($operation_rooms as $oproom){
                $start = explode(' ',Carbon::parse($o->date))[0];

                $datum= $x->getFirstFreeDateFromDate($oproom->id,$start,$o->clinic_id);
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
