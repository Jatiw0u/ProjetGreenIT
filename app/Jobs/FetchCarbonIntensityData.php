<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FetchCarbonIntensityData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $location;

    /**
     * Create a new job instance.
     */
    public function __construct($location)
    {
        $this->location = $location;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $id_location = $this->location->IdLocation;
        $lat = $this->location->Latitude;
        $lon = $this->location->Longitude;

        // Récupérer les données de l'API
        $response = Http::withHeaders([
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'no-store',
            'Expires' => '0',
            'Random-Header' => uniqid()
        ])->get("https://api.electricitymap.org/v3/carbon-intensity/latest", [
            'lat' => $lat,
            'lon' => $lon,
            'nocache' => uniqid()
        ]);

        $data = $response->json();

        if ($data && isset($data['carbonIntensity'])) {
            $datetime = (new \DateTime($data['datetime']))->format('Y-m-d H:i:s');
            $value = $data['carbonIntensity'];
            $emission_factor_type = $data['emissionFactorType'];

            // Insérer les données dans la table carbone_intensity
            DB::table('carbone_intensity')->updateOrInsert(
                ['IdLocation' => $id_location, 'DateTimeIntensity' => $datetime],
                ['Value' => $value, 'EmissionFactorType' => $emission_factor_type]
            );
        }
    }
}
