<?php
<<<<<<< HEAD

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
namespace App\Models;

>>>>>>> eed4e944ab0716d525b6f023761b73a1853fa534
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
<<<<<<< HEAD
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
=======
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
>>>>>>> eed4e944ab0716d525b6f023761b73a1853fa534
