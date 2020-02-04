<?php

namespace App\Services;

interface IOperatingRoomService
{
    function addOperatingRoom(array $operatingRoomData);
    function seeIfOpRoomBooked($id);
    function searchOperatingRooms($name, $number, $date);
}