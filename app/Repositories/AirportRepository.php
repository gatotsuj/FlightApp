<?php

namespace App\Repositories;

use App\Interfaces\AirportRepositoryInterface;
use App\Models\Airport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

class AirportRepository implements AirportRepositoryInterface
{
    public function getAllAirports(): Collection
    {
        return Airport::all();
    }

    public function getAirportBySlug($slug): ?Airport
    {
        try {
            return Airport::where('slug', $slug)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("Airport with slug {$slug} not found: ".$e->getMessage());

            return null;
        } catch (Throwable $e) {
            Log::error("Error retrieving airport with slug {$slug}: ".$e->getMessage());

            return null;
        }
    }

    public function getAirportByIataCode($iatacode): ?Airport
    {
        try {
            return Airport::where('iata_code', $iatacode)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("Airport with IATA code {$iatacode} not found: ".$e->getMessage());

            return null;
        } catch (Throwable $e) {
            Log::error("Error retrieving airport with IATA code {$iatacode}: ".$e->getMessage());

            return null;
        }
    }
}
