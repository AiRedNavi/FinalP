<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    /**
     * 1. Open-Meteo API (Cuaca Global - Tanpa API Key)
     * Mengambil data temperatur, curah hujan, kecepatan angin, dan risiko badai.
     */
    public function getWeather(float $latitude, float $longitude)
    {
        try {
            $response = Http::get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => ['temperature_2m', 'precipitation', 'wind_speed_10m', 'weather_code'],
                'timezone' => 'auto'
            ]);

            if ($response->successful()) {
                $data = $response->json('current');
                
                // Logika penentuan risiko badai sederhana dari kode cuaca (weather_code)
                // Misalnya weather_code >= 95 menandakan badai petir
                $stormRisk = ($data['weather_code'] >= 95) ? 'High' : 'Low';

                return [
                    'temperature' => $data['temperature_2m'],
                    'precipitation' => $data['precipitation'],
                    'wind_speed' => $data['wind_speed_10m'],
                    'storm_risk' => $stormRisk
                ];
            }
        } catch (\Exception $e) {
            Log::error("Open-Meteo API Error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * 2. World Bank API (Data Ekonomi Makro - Tanpa API Key)
     * Mengambil tren data Inflasi atau GDP berdasarkan kode ISO negara.
     */
    public function getEconomicData(string $isoCode, string $indicator = 'NY.GDP.MKTP.CD')
    {
        // Indikator default: NY.GDP.MKTP.CD (GDP)
        // Indikator alternatif: FP.CPI.TOTL.ZG (Inflasi)
        try {
            $response = Http::get("https://api.worldbank.org/v2/country/{$isoCode}/indicator/{$indicator}", [
                'format' => 'json',
                'per_page' => 5,
                'date' => '2020:2025' // Mengambil data historis beberapa tahun terakhir
            ]);

            if ($response->successful() && isset($response->json()[1])) {
                return $response->json()[1]; // Mengembalikan array data per tahun
            }
        } catch (\Exception $e) {
            Log::error("World Bank API Error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * 3. ExchangeRate API (Kurs Mata Uang Real-time)
     */
    public function getExchangeRate(string $baseCurrency = 'USD')
    {
        try {
            // Gunakan endpoint publik tanpa key atau isi dengan key Anda jika mendaftar
            $response = Http::get("https://open.er-api.com/v6/latest/{$baseCurrency}");

            if ($response->successful()) {
                return $response->json('rates'); // Mengembalikan daftar kurs mata uang lengkap
            }
        } catch (\Exception $e) {
            Log::error("ExchangeRate API Error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * 4. GNews API (Berita Logistik & Ekonomi - Butuh API Key Gratis)
     */
    public function getNews(string $countryName)
    {
        $apiKey = env('GNEWS_API_KEY'); // Jangan lupa tambahkan di file .env nanti
        
        if (!$apiKey) {
            return [];
        }

        try {
            $query = "{$countryName} AND (logistics OR trade OR shipping OR economy)";
            $response = Http::get("https://gnews.io/api/v4/search", [
                'q' => $query,
                'lang' => 'en',
                'apikey' => $apiKey,
                'max' => 5 // Cukup ambil 5 berita agar hemat kuota
            ]);

            if ($response->successful()) {
                return $response->json('articles');
            }
        } catch (\Exception $e) {
            Log::error("GNews API Error: " . $e->getMessage());
        }

        return [];
    }
    /**
 * 5. REST Countries API (Profil Dasar Negara - Tanpa API Key)
 */
    public function getCountryProfile(string $isoCode)
    {
        try {
            // Menggunakan kode ISO untuk mencari detail negara
            $response = Http::get("https://restcountries.com/v3.1/alpha/{$isoCode}");

            if ($response->successful() && isset($response->json()[0])) {
                $data = $response->json()[0];
                
                return [
                    'languages' => isset($data['languages']) ? implode(', ', array_values($data['languages'])) : 'N/A',
                    'region' => $data['region'] ?? 'N/A',
                    'subregion' => $data['subregion'] ?? 'N/A'
                ];
            }
        } catch (\Exception $e) {
            Log::error("REST Countries API Error: " . $e->getMessage());
        }

        return null;
    }
}