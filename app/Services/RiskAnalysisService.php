<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use App\Models\RiskScore;
use App\Models\Country;

class RiskAnalysisService
{
    /**
     * 1. AI Feature: Lexicon Based Sentiment Analysis
     * Memproses teks berita untuk menghitung persentase sentimen menggunakan kamus database.
     */
    public function analyzeSentiment(string $text): array
    {
        // Ambil semua kamus kata dari database dan ubah ke bentuk array lowercase
        $positiveWords = PositiveWord::pluck('word')->map(fn($w) => strtolower($w))->toArray();
        $negativeWords = NegativeWord::pluck('word')->map(fn($w) => strtolower($w))->toArray();

        // Bersihkan teks berita dari tanda baca dan ubah ke lowercase
        $cleanedText = preg_replace('/[^\w\s]/', '', strtolower($text));
        
        // Pecah teks menjadi kumpulan kata (Tokenisasi)
        $words = explode(' ', $cleanedText);

        $positiveScore = 0;
        $negativeScore = 0;

        // Proses pencocokan kata dengan kamus
        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveScore++;
            }
            if (in_array($word, $negativeWords)) {
                $negativeScore++;
            }
        }

        $totalMatches = $positiveScore + $negativeScore;

        // Hitung persentase output sesuai spesifikasi PDF
        if ($totalMatches > 0) {
            $positivePercent = round(($positiveScore / $totalMatches) * 100);
            $negativePercent = round(($negativeScore / $totalMatches) * 100);
            $neutralPercent = 0;
        } else {
            $positivePercent = 0;
            $negativePercent = 0;
            $neutralPercent = 100;
        }

        // Tentukan label sentimen dominan
        $sentiment = 'Neutral';
        if ($positiveScore > $negativeScore) {
            $sentiment = 'Positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentiment = 'Negative';
        }

        return [
            'positive_percent' => $positivePercent,
            'neutral_percent' => $neutralPercent,
            'negative_percent' => $negativePercent,
            'sentiment' => $sentiment
        ];
    }

    /**
     * 2. AI Feature: Supply Chain Risk Prediction
     * Menggunakan Weighted Risk Model untuk menghasilkan total skor risiko negara (Skala 0-100).
     */
    public function calculateCountryRisk(int $countryId, array $weatherData, float $inflationRate, string $dominantSentiment): RiskScore
    {
        // A. Konversi Data Cuaca ke Skala Risiko (0 - 100)
        // Jika badai berisiko tinggi atau curah hujan sangat ekstrem, skor maksimal
        $weatherRisk = 20.00; 
        if (($weatherData['storm_risk'] ?? 'Low') === 'High' || ($weatherData['precipitation'] ?? 0) > 50) {
            $weatherRisk = 90.00;
        } elseif (($weatherData['wind_speed'] ?? 0) > 30) {
            $weatherRisk = 60.00;
        }

        // B. Konversi Data Inflasi ke Skala Risiko (0 - 100)
        // Inflasi tinggi di atas target normal meningkatkan biaya produksi dan risiko supply chain
        $inflationRisk = 20.00;
        if ($inflationRate > 10.00) {
            $inflationRisk = 95.00;
        } elseif ($inflationRate > 5.00) {
            $inflationRisk = 60.00;
        }

        // C. Konversi Sentimen Berita Geopolitik/Logistik ke Skala Risiko (0 - 100)
        $sentimentRisk = 50.00; // Default Neutral
        if ($dominantSentiment === 'Negative') {
            $sentimentRisk = 90.00;
        } elseif ($dominantSentiment === 'Positive') {
            $sentimentRisk = 15.00;
        }

        // D. Konversi Risiko Nilai Tukar (Exchange Rate Risk)
        // Untuk prototipe awal, kita beri nilai acak/stabil terukur atau dinamis (0 - 100)
        $exchangeRateRisk = ($inflationRate > 5.00) ? 70.00 : 30.00;

        // E. Perhitungan Menggunakan Rumus Weighted Risk Model
        // Bobot: Cuaca (30%), Inflasi (20%), Berita/Politik (40%), Mata Uang (10%)
        $totalRisk = ($weatherRisk * 0.30) + ($inflationRisk * 0.20) + ($sentimentRisk * 0.40) + ($exchangeRateRisk * 0.10);

        // Simpan hasil kalkulasi analitik ke database riwayat tren risiko
        return RiskScore::create([
            'country_id' => $countryId,
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'exchange_rate_risk' => $exchangeRateRisk,
            'sentiment_risk' => $sentimentRisk,
            'total_risk' => round($totalRisk, 2)
        ]);
    }
}