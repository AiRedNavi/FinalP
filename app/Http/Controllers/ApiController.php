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

    /**
     * Inject Service yang dibutuhkan.
     */
    public function __construct(ApiService $apiService, RiskAnalysisService $riskService)
    {
        $this->apiService = $apiService;
        $this->riskService = $riskService;
    }

    /**
     * 1. GET /api/countries
     */
    public function getCountries(Request $request)
    {
        $countryId = $request->query('country_id');

        if ($countryId) {
            $country = Country::find($countryId);
            if (!$country) {
                return response()->json(['status' => 'error', 'message' => 'Country not found'], 404);
            }
            
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
     * 2. GET /api/risk
     */
    public function getRiskData(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);
        
        $country = Country::with('ports')->find($request->country_id);

        $port = $country->ports->first();
        $lat = $port ? $port->latitude : -6.10; 
        $lng = $port ? $port->longitude : 106.89;

        // Ambil Data
        $weather = $this->apiService->getWeather($lat, $lng) ?? ['storm_risk' => 'Low', 'precipitation' => 0];
        $latestNews = NewsCache::where('country_id', $country->id)->latest()->first();
        $sentimentLabel = $latestNews ? $latestNews->sentiment : 'Neutral';
        $currencyRates = $this->apiService->getExchangeRate('USD');
        
        // Kalkulasi Engine
        $calculatedRisk = $this->riskService->calculateCountryRisk(
            $country->id, 
            $weather, 
            $country->inflation ?? 2.0, 
            $sentimentLabel,
            $currencyRates ?? [] 
        );

        // Ekstraksi Nilai untuk Database
        $totalRisk = is_array($calculatedRisk) ? ($calculatedRisk['total_risk'] ?? 33) : ($calculatedRisk->total_risk ?? 33);
        $weatherRisk = is_array($calculatedRisk) ? ($calculatedRisk['weather_risk'] ?? 0) : ($calculatedRisk->weather_risk ?? 0);
        $inflationRisk = is_array($calculatedRisk) ? ($calculatedRisk['inflation_risk'] ?? 0) : ($calculatedRisk->inflation_risk ?? 0);
        $sentimentRisk = is_array($calculatedRisk) ? ($calculatedRisk['sentiment_risk'] ?? 0) : ($calculatedRisk->sentiment_risk ?? 0);
        $exchangeRateRisk = is_array($calculatedRisk) ? ($calculatedRisk['exchange_rate_risk'] ?? 0) : ($calculatedRisk->exchange_rate_risk ?? 0);

        RiskScore::create([
            'country_id'          => $country->id,
            'total_risk'          => $totalRisk,
            'weather_risk'        => $weatherRisk,
            'inflation_risk'      => $inflationRisk,
            'sentiment_risk'      => $sentimentRisk,
            'exchange_rate_risk'  => $exchangeRateRisk,
        ]);

        $trends = RiskScore::where('country_id', $country->id)->latest()->take(5)->get()->reverse();

        // FIX: Kirim sebagai OBJEK agar frontend bisa mengakses .total_risk
        $currentRiskResponse = [
            'total_risk'         => $totalRisk,
            'weather_risk'       => $weatherRisk,
            'inflation_risk'     => $inflationRisk,
            'sentiment_risk'     => $sentimentRisk,
            'exchange_rate_risk' => $exchangeRateRisk,
        ];

        return response()->json([
            'status' => 'success',
            'current_risk' => $currentRiskResponse,
            'weather_info' => $weather,
            'trends' => $trends
        ]);
    }

    /**
     * 3. GET /api/news
     */
    public function getNewsData(Request $request)
    {
        $request->validate(['country_id' => 'required|exists:countries,id']);
        $country = Country::find($request->country_id);

        $cachedNews = NewsCache::where('country_id', $country->id)
                                ->where('created_at', '>=', Carbon::now()->subHour())
                                ->get();

        if ($cachedNews->isNotEmpty()) {
            return response()->json(['status' => 'success', 'source' => 'cache', 'data' => $cachedNews]);
        }

        $articles = $this->apiService->getNews($country->name) ?? [];

        NewsCache::where('country_id', $country->id)->delete();

        $savedNews = [];
        foreach ($articles as $article) {
            $fullText = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
            $analysis = $this->riskService->analyzeSentiment($fullText);

            $news = NewsCache::create([
                'country_id' => $country->id,
                'title' => substr($article['title'] ?? 'No Title', 0, 255),
                'source' => $article['source']['name'] ?? 'Unknown',
                'url' => $article['url'] ?? '#',
                'positive_count' => $analysis['positive_percent'] ?? 0, 
                'negative_count' => $analysis['negative_percent'] ?? 0,
                'sentiment' => $analysis['sentiment'] ?? 'Neutral',
                'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt']) : Carbon::now(),
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
     * 5. GET /api/currency
     */
    public function getCurrencyData(Request $request)
    {
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

    /**
     * 6. GET /api/ports
     */
    public function getPorts(Request $request)
    {
        $search = $request->query('search');

        if ($search) {
            $ports = Port::where('name', 'LIKE', "%{$search}%")
                         ->with('country')
                         ->get();
        } else {
            $ports = Port::with('country')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $ports
        ]);
    }
}