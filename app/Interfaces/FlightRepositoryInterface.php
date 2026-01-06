<?php

namespace App\Interfaces;

interface FlightRepositoryInterface
{
    public function getAllFlights();

    public function getFlightByFlightNumber($flightNumber);
}
