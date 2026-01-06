<?php

namespace App\Interfaces;

interface AirlineRepositoryInterface
{
    public function getAllAirlines();

    public function getAirlineById($id);

    public function createAirline(array $airlineDetails);

    public function updateAirline($id, array $newDetails);

    public function deleteAirline($id);
}
