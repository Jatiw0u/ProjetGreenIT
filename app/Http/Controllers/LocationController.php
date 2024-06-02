<?php
namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function create()
    {
        $locations = Location::all(); // Récupérer toutes les villes depuis la base de données
        return view('carbonIntensity', compact('locations'));
    }
}