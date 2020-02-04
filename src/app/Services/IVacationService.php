<?php

namespace App\Services;

interface IVacationService
{
    function getVacationRequests();
    function approveVacationRequest($id);
}