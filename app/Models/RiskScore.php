<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id', 'weather_risk', 'inflation_risk', 
        'exchange_rate_risk', 'sentiment_risk', 'total_risk'
    ]; // Sesuai kolom database[cite: 2]

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}