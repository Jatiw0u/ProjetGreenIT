<?php
namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function create()
    {
        $locations = Location::all(); // Récupérer toutes les villes depuis la base de données

        if (request()->routeIs('intensity')) {
            return view('carbonIntensity', compact('locations'));
        }

        if (request()->routeIs('energy-demand')) {
            return view('energyDemand', compact('locations'));
        }
    }
}