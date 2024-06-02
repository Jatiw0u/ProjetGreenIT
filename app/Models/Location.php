<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'location';

    // Définition des éléments de la table
    protected $fillable = [
        'NameLocation',
        'Country',
        'longitude',
        'latitude',
    ];

    // Si les timestamps ne sont pas utilisés, vous pouvez les désactiver
    public $timestamps = false;

    public function carboneIntensities()
    {
        return $this->hasMany(carbone_intensity::class, 'IdLocation', 'IdLocation');
    }

}
