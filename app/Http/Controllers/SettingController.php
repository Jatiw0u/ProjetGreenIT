<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Carbon\Carbon;

class SettingController extends Controller
{
    // requête GET /parametres/{idLocation}
    public function getSettingsByLocation($idLocation)
    {
        // Récupération des paramètres en fonction de l'ID de localisation
        $settings = Setting::where('id_location_Setting', $idLocation)->get();

        if ($settings->isEmpty()) {
            return response()->json(['message' => 'No settings found for this location.'], 404);
        }

        return response()->json($settings, 200);
    }

    // requête POST /parametres/{value}/{idLocation}
    public function updateSettingByLocation($value, $idLocation)
    {
        
        $setting = Setting::where('id_location_Setting', $idLocation)->first();

        //Si le paramètre est inexistant, on crée la ligne dans la base
        if (!$setting) {
            $setting = new Setting();
            $setting->id_location_Setting = $idLocation;
            $setting->DateSetting = Carbon::now();
        }

        // Mise à jour de la valeur du paramètre
        $setting->Number_Setting = $value;
        $setting->save();

        return response()->json(['message' => 'Setting updated successfully.'], 200);
    }
}

?>
