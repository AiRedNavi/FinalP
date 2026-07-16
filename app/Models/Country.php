<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'iso_code', 'currency_code', 'region', 'gdp', 'inflation', 'population']; // Sesuai kolom database

    // Relasi: Satu negara punya banyak pelabuhan
    public function ports()
    {
        return $this->hasMany(Port::class);
    }   

    // Relasi: Satu negara punya banyak riwayat skor risiko
    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }
}