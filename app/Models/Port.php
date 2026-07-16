<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = ['country_id', 'name', 'latitude', 'longitude']; // Sesuai kolom database

    // Relasi balik ke negara
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}