<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id', 
        'total_risk', 
        'weather_risk', 
        'inflation_risk', 
        'sentiment_risk',
        'exchange_rate_risk'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}