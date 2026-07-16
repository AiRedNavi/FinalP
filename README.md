# FinalP
proyek akhir pemrograman web Muhammad Alif Ivander

PROJECT FINAL 
Global Supply Chain Risk Intelligence Platform (Platform Monitoring 
Risiko Rantai Pasok Global Berbasis Multi-API dan Analitik Data)
Spesifikasi:
Project ini sangat bergantung pada data untuk:
• Mengelola risiko logistik 
• Memantau kondisi cuaca ekstrem 
• Menganalisis gangguan transportasi 
• Mengamati kondisi ekonomi suatu negara 
• Membantu pengambilan keputusan bisnis 
Project ini memperlihatkan kemampuan mahasiswa dalam:
• Full Stack Development 
• API Integration 
• Data Engineering 
• Dashboard Analytics 
• Geospatial Visualization 
• Business Intelligence 
• Decision Support System 
Studi Kasus
Sebuah perusahaan ingin mengimpor barang dari berbagai negara.
Masalah:
• Cuaca buruk dapat mengganggu pengiriman. 
• Nilai tukar mata uang berubah. 
• Konflik geopolitik meningkatkan risiko. 
• Kemacetan pelabuhan menyebabkan keterlambatan. 
• Inflasi suatu negara mempengaruhi biaya produksi. 
Dibangunlah sistem yang dapat memantau seluruh indikator tersebut dalam satu dashboard.
Teknologi
Backend
• PHP 
• Laravel 
• MySQL 
Frontend
• Bootstrap 5 
• AJAX 
• JavaScript ES6 
Visualisasi
• Chart.js 
• Leaflet.js 
Deployment
• Docker (opsional) 
• GitHub 
API GRATIS YANG DIGUNAKAN
1. Open-Meteo API
Cuaca global.
Website:
Open-Meteo API
Data:
• Temperatur 
• Curah hujan 
• Kecepatan angin 
• Risiko badai 
Tidak membutuhkan API Key.
2. World Bank API
Website:
World Bank API
Data:
• GDP 
• Inflasi 
• Populasi 
• Ekspor 
• Impor 
3. REST Countries API
Website:
REST Countries API
Data:
• Negara 
• Mata uang 
• Wilayah 
• Bahasa 
4. ExchangeRate API
Website:
ExchangeRate API
Data:
• Kurs mata uang real-time 
5. Marine Traffic Alternative API (Gratis)
Gunakan data pelabuhan publik dari:
World Port Index Dataset
Data:
• Pelabuhan 
• Lokasi pelabuhan 
• Negara 
6. News API Alternatif Gratis
Gunakan:
GNews API
Data:
• Berita ekonomi 
• Berita logistik 
• Berita geopolitik 
7. OpenStreetMap
Menggunakan: Leaflet.js
Fitur Utama
1. Global Country Dashboard
User memilih negara.
Contoh:
Germany
China
Indonesia
Australia
Sistem menampilkan:
• GDP 
• Inflasi 
• Populasi 
• Mata uang 
• Cuaca saat ini 
2. Risk Scoring Engine
Sistem menghitung:
Risk Score =
Weather +
Inflation +
Exchange Rate +
News Sentiment
Output:
Germany : 22 (Low Risk)
China : 47 (Medium Risk)
Mahasiswa membuat algoritmanya sendiri.
3. Global Weather Monitoring
Peta dunia menunjukkan:
• Hujan 
• Badai 
• Angin kencang 
berdasarkan negara yang dipilih.
4. Currency Impact Dashboard
Menampilkan:
• Nilai tukar 
• Grafik perubahan kurs 
Menggunakan Chart.js.
5. News Intelligence
Menampilkan berita terkait:
• Logistics 
• Trade 
• Shipping 
• Economy 
Menggunakan GNews API.
6. Port Location Dashboard
Menampilkan lokasi pelabuhan dunia.
Fitur:
• Cari pelabuhan 
• Cari negara 
• Marker interaktif 
7. Data Visualization Dashboard
Grafik:
• GDP Trend 
• Inflation Trend 
• Currency Trend 
• Risk Trend 
8. Country Comparison Engine
Bandingkan:
Germany vs Australia
GDP
Inflation
Risk
Weather
Currency
9. Favorite Monitoring List
User dapat menyimpan negara yang dipantau.
10. Admin Dashboard
Kelola:
• User 
• Dataset pelabuhan 
• Artikel analisis 
Fitur AI / Data Science (Tanpa AI Berbayar)
Sentiment Analysis Berita
Menggunakan:
Lexicon Based Sentiment Analysis
dibuat dengan PHP.
Output:
Positive : 60%
Neutral : 25%
Negative : 15%
Contoh:
Mahasiswa cukup membuat kamus kata (dictionary) positif dan negatif.
Contoh tabel database:
positive_words
id word
1 growth
2 increase
3 profit
4 stable
5 improve
negative_words
id word
1 war
2 crisis
3 inflation
4 delay
5 disaster
Misalnya berita:
"Inflation increases while exports decrease due to war."
Maka program akan:
Positive: increase
Negative: inflation
 war
 decrease
Hasil:
Positive : 1
Negative : 3
Sentiment = Negative
Coding PHP sederhana:
$positiveScore = 0;
$negativeScore = 0;
foreach ($words as $word) {
 if (in_array($word, $positiveWords)) {
 $positiveScore++;
 }
 if (in_array($word, $negativeWords)) {
 $negativeScore++;
 }
}
$sentiment = $positiveScore > $negativeScore
 ? "Positive"
 : "Negative";
Supply Chain Risk Prediction
Metode:
Weighted Risk Model
atau
Simple Scoring Algorithm
Contoh:
Weather Risk = 30%
Inflation Risk = 20%
Political News Risk = 40%
Currency Risk = 10%
Total Risk = 28%
Database
users
countries
risk_scores
news_cache
ports
watchlists
articles
REST API yang Harus Dibuat Mahasiswa
GET /api/countries
GET /api/risk
GET /api/ports
GET /api/news
GET /api/currency
Perkiraan Skala Project
• 15–20 tabel database 
• 30+ endpoint 
• 6–7 API eksternal 
• Dashboard analitik kompleks 
• Sistem scoring dan prediksi 
• Peta interaktif global 
