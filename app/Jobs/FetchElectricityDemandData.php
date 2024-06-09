<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FetchElectricityDemandData implements ShouldQueue
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
        ])->get("https://api.electricitymap.org/v3/power-breakdown/latest", [
            'lat' => $lat,
            'lon' => $lon,
            'nocache' => uniqid()
        ]);

        $data = $response->json();

        if ($data && isset($data['powerConsumptionBreakdown'])) {
            $datetime = (new \DateTime($data['datetime']))->format('Y-m-d H:i:s');
            $consumption = $data['powerConsumptionBreakdown'];

            // Insérer les données dans la table electrical_demand
            DB::table('electrical_demand')->updateOrInsert(
                ['Id_Location' => $id_location, 'DateTimeDemand' => $datetime],
                [
                    'ValueNuclear' => $consumption['nuclear'] ?? null,
                    'ValueGeothermal' => $consumption['geothermal'] ?? null,
                    'ValueBiomass' => $consumption['biomass'] ?? null,
                    'ValueCoal' => $consumption['coal'] ?? null,
                    'ValueWind' => $consumption['wind'] ?? null,
                    'ValueSolar' => $consumption['solar'] ?? null,
                    'ValueHydro' => $consumption['hydro'] ?? null,
                    'ValueGas' => $consumption['gas'] ?? null,
                    'ValueOil' => $consumption['oil'] ?? null,
                    'ValueUnknown' => $consumption['unknown'] ?? null,
                    'ValueHydroDischarge' => $consumption['hydro discharge'] ?? null,
                    'ValueBatteryDischarge' => $consumption['battery discharge'] ?? null,
                ]
            );
        }
    }
}

?>
