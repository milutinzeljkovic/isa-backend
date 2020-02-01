<?php

namespace App\Services;

interface IPatientService
{
    function getPatientsByClinic();
    function searchPatients(array $searchParameters);
    function getMedicalRecord($id);
}