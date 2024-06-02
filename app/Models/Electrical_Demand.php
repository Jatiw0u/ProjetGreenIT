<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    
}
