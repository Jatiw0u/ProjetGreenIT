<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\carbone_intensity;
use App\Models\Electrical_Demand;

class ApiController extends Controller
{
    public function List_City()
    {
        // Récupérer toutes les villes de la table location
        $locations = Location::all();

        // Retourner les données en format JSON
        return response()->json(['locations' => $locations]);
    }

    public function List_CarboneIntensitiesLocation($id)
    {
        // Récupérer toutes les intensités de carbone pour la localisation donnée
        $intensities = carbone_intensity::where('IdLocation', $id)->get(['IdIntensityCarbone', 'DateTimeIntensity', 'value', 'EmissionFactorType']);

        // Retourner les données en format JSON
        return response()->json(['carbone_intensities' => $intensities]);
    }

    public function List_ElectricalDemandLocation($id)
    {
        // Récupérer toutes les demandes électriques pour la localisation donnée
        $demands = Electrical_Demand::where('Id_Location', $id)->get([
            'Id_Demand', 
            'DateTimeDemand', 
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
            'ValueBatteryDischarge'
        ]);

        // Retourner les données en format JSON
        return response()->json(['electrical_demands' => $demands]);
    }

}
