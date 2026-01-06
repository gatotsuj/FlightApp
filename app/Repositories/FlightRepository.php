<?php

namespace App\Repositories;

use App\Interfaces\FlightRepositoryInterface;
use App\Models\Flight;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

class FlightRepository implements FlightRepositoryInterface
{
    public function getAllFlights($filter = null): Collection
    {
        $flights = Flight::query();

        if (! empty($filter['departure'])) {
            $flights->whereHas('segments', function ($query) use ($filter) {
                $query->where('airport_id', $filter['departure'])
                    ->where('sequence', 1);
            });
        }

        if (! empty($filter['destination'])) {
            $flights->whereHas('segments', function ($query) use ($filter) {
                $query->where('airport_id', $filter['destination'])
                    ->orderBy('sequence', 'desc')
                    ->limit(1);
            });
        }

        if (! empty($filter['date'])) {
            $flights->whereHas('segments', function ($query) use ($filter) {
                $query->whereDate('time', $filter['date']);
            });
        }

        return $flights->get();
    }

    public function getFlightByFlightNumber($flightNumber): ?Flight
    {
        try {
            return Flight::where('flight_number', $flightNumber)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("Flight with flight number {$flightNumber} not found: ".$e->getMessage());

            return null;
        } catch (Throwable $e) {
            Log::error("Error retrieving flight with flight number {$flightNumber}: ".$e->getMessage());

            return null;
        }
    }
}
