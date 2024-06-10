<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Nom de la table associée
    protected $table = 'settings';
    protected $primaryKey = 'ID_Setting'; // clé primaire

    public $timestamps = false;

    // Colonnes pouvant être assignées en masse
    protected $fillable = ['DateSetting', 'id_location_Setting', 'Number_Setting'];
}

?>
