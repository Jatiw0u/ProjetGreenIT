<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Jobs\FetchCarbonIntensityData;

class carbone_intensity extends Model
{
    use HasFactory;

    protected $table = 'carbone_intensity';

    protected $fillable = [
        'IdLocation',
        'value',
        'DateTimeIntensity',
        'EmissionFactorType',
    ];

    public $timestamps = false;

    public function location()
    {
        return $this->belongsTo(Location::class, 'IdLocation', 'IdLocation');
    }

    public static function updateCarbonIntensityData()
    {
        // Vérifier si la table carbone_intensity est vide
        $row_count = DB::table('carbone_intensity')->count();

        // Récupérer les coordonnées de chaque ville
        $locations = DB::table('location')->get();

        foreach ($locations as $location) {
            FetchCarbonIntensityData::dispatch($location)->delay(now()->addSeconds(1));
        }

        return 'Données de l\'intensité carbone mises à jour avec succès.';
    }
}
