<?php

namespace App\Services;

use App\Services\IMedicineService;
use App\Medicine;
use DB;

class MedicineService implements IMedicineService
{
    public function addMedicine(array $medicineData)
    {
        $medicine = new Medicine();

        $medicine->name = array_get($medicineData, 'name');
        $medicine->label = array_get($medicineData, 'label');

        $medicine->save();
       

        return response()->json(['created' => 'Medicine has been created'], 201);
    }

    public function getMedicines()
    {
        $medicines = DB::table('medicines')->get();

        return $medicines;
    }



}