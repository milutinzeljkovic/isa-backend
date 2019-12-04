<?php

namespace App\Services;

use App\Services\IMedicineService;
use App\Medicine;

class MedicineService implements IMedicineService
{
    public function addMedicine(array $medicineData)
    {
        $medicine = new Medicine();

        $medicine->name = array_get($medicineData, 'name');
        $medicine->save();
       

        return response()->json(['created' => 'Medicine has been created'], 201);
    }

}