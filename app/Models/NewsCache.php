<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    protected $fillable = [
        'country_id', 'title', 'source', 'url', 
        'positive_count', 'negative_count', 'sentiment', 'published_at'
    ]; // Sesuai kolom database[cite: 2]
}