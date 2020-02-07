<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Prescription;
use Illuminate\Support\Facades\DB;
use Auth;

class PrescriptionController extends Controller
{
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
        $user = Auth::user();
        $nurse = $user->userable()->first();

        DB::transaction(function () use($id, $nurse)
        {
            $pr =  DB::table('prescriptions')
                ->where('id', $pr)
                ->first();
            DB::table('prescriptions')
                ->where('id', $pr)
                ->where('lock_version', $pr->lock_version)
                ->update([
                        'lock_version' => $pr->lock_version+1,
                        'nurse_id' => $nurse
                    ]);            
        });

        $updated = Prescription::find($id);

        if($updated->nurse_id != $nurse)
        {
            return response('Error '.json_encode($updatedClinic),400);
        }
        else
        {
            return $updated;
        }


    }

    function getPrescriptions(){
        $prescriptions = Prescription::where('nurse_id',null)->get();
        return $prescriptions;
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
}
