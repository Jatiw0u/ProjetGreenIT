<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


}
