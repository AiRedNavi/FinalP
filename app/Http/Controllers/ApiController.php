<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Port;
use App\Models\NewsCache;
use App\Models\RiskScore;
use App\Services\ApiService;
use App\Services\RiskAnalysisService;
use Carbon\Carbon;

class ApiController extends Controller
{
    protected $apiService;
    protected $riskService;

    // Inject kedua Service yang telah dibuat di Fase 3 & Fase 4[cite: 1]
    public function __construct(ApiService $apiService, RiskAnalysisService $riskService)
    {
        $this->apiService = $apiService;
        $this->riskService = $riskService;
    }

    /**
     * 1. GET /api/countries[cite: 1]
     * Mengembalikan profil negara beserta data makroekonomi untuk dashboard global[cite: 1].
     */
    public function getCountries(Request $request)
    {
        $countryId = $request->query('country_id');

        if ($countryId) {
            $country = Country::find($countryId);
            if (!$country) return response()->json(['message' => 'Country not found'], 404);
            
            // Integrasikan data profil dari REST Countries API secara real-time[cite: 1]
            $profile = $this->apiService->getCountryProfile($country->iso_code);
            
            return response()->json([
                'status' => 'success',
                'data' => array_merge($country->toArray(), $profile ?? [])
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => Country::all()
        ]);
    }

    /**
     * 2. GET /api/risk[cite: 1]
     * Mengambil hasil kalkulasi Weighted Risk Score & riwayat tren risiko negara[cite: 1].
     */
    public function getRiskData(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);
        $country = Country::find($request->country_id);

        // Ambil data dasar pelabuhan pertama negara tersebut sebagai koordinat acuan cuaca
        $port = Port::where('country_id', $country->id)->first();
        $lat = $port ? $port->latitude : -6.10; // Default fallback ke koordinat seeder jika tidak ada pelabuhan[cite: 2]
        $lng = $port ? $port->longitude : 106.89;

        // 1. Ambil Data Cuaca[cite: 1]
        $weather = $this->apiService->getWeather($lat, $lng);

        // 2. Ambil Berita Dominan untuk mengambil teks analisis sentimen
            $latestNews = NewsCache::where('country_id', $country->id)->latest()->first();
            $sentimentLabel = $latestNews ? $latestNews->sentiment : 'Neutral';

        // 3. Hitung skor risiko menggunakan Engine weighted model di Fase 4
        $calculatedRisk = $this->riskService->calculateCountryRisk(
            $country->id,
            $weather ?? ['storm_risk' => 'Low', 'precipitation' => 0],
            $country->inflation ?? 2.0,
            $sentimentLabel
        );

        // 4. Ambil 5 riwayat tren risiko terakhir untuk kebutuhan Chart.js
        $trends = RiskScore::where('country_id', $country->id)->latest()->take(5)->get()->reverse();

        return response()->json([
            'status' => 'success',
            'current_risk' => $calculatedRisk,
            'weather_info' => $weather,
            'trends' => $trends
        ]);
    }

    /**
     * 3. GET /api/ports[cite: 1]
     * Menyediakan data koordinat pelabuhan untuk peta interaktif Leaflet.js[cite: 1].
     */
    public function getPortData(Request $request)
    {
        $query = Port::with('country');

        // Fitur Cari Pelabuhan atau Cari Negara sesuai PDF[cite: 1]
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('country', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    /**
     * 4. GET /api/news[cite: 1]
     * Mengambil berita logistik dari API luar, menganalisis sentimen, dan menyimpannya ke Cache[cite: 1].
     */
    public function getNewsData(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);
        $country = Country::find($request->country_id);

        // Cek apakah ada cache berita yang berumur kurang dari 1 jam untuk menghemat kuota API gratis[cite: 1]
        $cachedNews = NewsCache::where('country_id', $country->id)
                                ->where('created_at', '>=', Carbon::now()->subHour())
                                ->get();

        if ($cachedNews->isNotEmpty()) {
            return response()->json(['status' => 'success', 'source' => 'cache', 'data' => $cachedNews]);
        }

        // Jika cache kedaluwarsa, tembak GNews API[cite: 1]
        $articles = $this->apiService->getNews($country->name);

        // Hapus cache berita lama agar database tidak penuh
        NewsCache::where('country_id', $country->id)->delete();

        $savedNews = [];
        foreach ($articles as $article) {
            // Jalankan Fitur AI: Lexicon Sentiment Analysis pada konten berita[cite: 1]
            $fullText = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
            $analysis = $this->riskService->analyzeSentiment($fullText);

            // Simpan ke database
            $news = NewsCache::create([
                'country_id' => $country->id,
                'title' => substr($article['title'] ?? 'No Title', 0, 255),
                'source' => $article['source']['name'] ?? 'Unknown',
                'url' => $article['url'] ?? '#',
                'positive_count' => $analysis['positive_percent'], 
                'negative_count' => $analysis['negative_percent'],
                'sentiment' => $analysis['sentiment'], // PENTING: Pastikan bersih seperti ini
                'published_at' => Carbon::parse($article['publishedAt'] ?? now()),
            ]);

            $savedNews[] = $news;
        }

        return response()->json([
            'status' => 'success',
            'source' => 'api_live',
            'data' => $savedNews
        ]);
    }

    /**
     * 5. GET /api/currency[cite: 1]
     * Menyediakan data nilai tukar real-time untuk Currency Impact Dashboard[cite: 1].
     */
    public function getCurrencyData(Request $request)
    {
        // Mengambil rate mata uang global dengan base USD[cite: 1]
        $rates = $this->apiService->getExchangeRate('USD');

        if (!$rates) {
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch currency rates'], 500);
        }

        return response()->json([
            'status' => 'success',
            'base' => 'USD',
            'rates' => $rates
        ]);
    }
}