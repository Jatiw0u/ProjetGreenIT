<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'location'; // Nom de la table dans la base de données

    // Définir les attributs de la table
    protected $fillable = [
        'idLocation',
        'NameLocation',
        'Country',
        'Longitude',
        'Latitude',
    ];
}