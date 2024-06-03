<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'location';

    protected $fillable = [
        'idLocation',
        'NameLocation',
        'Country',
        'longitude',
        'latitude',
    ];

    public $timestamps = false;

    public function carboneIntensities()
    {
        return $this->hasMany(CarboneIntensity::class, 'IdLocation', 'IdLocation');
    }

    public function electricalDemands()
    {
        return $this->hasMany(Electrical_Demand::class, 'Id_Location', 'IdLocation');
    }
}
