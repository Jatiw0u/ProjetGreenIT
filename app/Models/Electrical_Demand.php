<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Jobs\FetchElectricityDemandData;

class Electrical_Demand extends Model
{
    use HasFactory;

    protected $table = 'electrical_demand';

    protected $fillable = [
        'DateTimeDemand',
        'Id_Location',
        'ValueNuclear',
        'ValueGeothermal',
        'ValueBiomass',
        'ValueCoal',
        'ValueWind',
        'ValueSolar',
        'ValueHydro',
        'ValueGas',
        'ValueOil',
        'ValueUnknown',
        'ValueHydroDischarge',
        'ValueBatteryDischarge',
    ];

    public $timestamps = false;

    // Définir la relation avec Location
    public function location()
    {
        return $this->belongsTo(Location::class, 'Id_Location', 'IdLocation');
    }

    public static function updateElectricityDemandData()
    {
        $row_count = DB::table('electrical_demand')->count();
        $locations = DB::table('location')->get();

        foreach ($locations as $location) {
            FetchElectricityDemandData::dispatch($location)->delay(now()->addSeconds(1));
        }

        return 'Données de demande électrique mises à jour avec succès.';
    }
    
}
