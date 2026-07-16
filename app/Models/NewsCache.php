<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    // Mengunci nama tabel agar Laravel tidak otomatis mencari 'news_caches'
    protected $table = 'news_cache'; 

    // Properti lainnya (fillable, dll) tetap biarkan saja
    protected $fillable = ['country_id', 'title', 'source', 'url', 'positive_count', 'negative_count', 'sentiment'];
}