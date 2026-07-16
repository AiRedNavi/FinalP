<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet.js CSS (Untuk Peta Interaktif) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; border-radius: 8px; }
        .card-custom { margin-bottom: 20px; }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar Utama -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🌐 Supply Chain Risk Platform</span>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Fitur 1: Dropdown Pilih Negara -->
        <div class="row card-custom">
            <div class="col-md-4">
                <label for="countrySelect" class="form-label fw-bold">Pilih Negara Pemantauan:</label>
                <select id="countrySelect" class="form-select">
                    <!-- Data akan diisi otomatis oleh AJAX dari /api/countries -->
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Tampilan Kiri: Profil & Peta Pelabuhan -->
            <div class="col-md-7 card-custom">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-whitefw-bold">Port & Logistics Geospatial Map</div>
                    <div class="card-body">
                        <!-- Input Cari Pelabuhan/Negara -->
                        <div class="input-group mb-3">
                            <input type="text" id="searchPort" class="form-control" placeholder="Cari pelabuhan atau negara...">
                            <button class="btn btn-outline-secondary" type="button" id="btnSearch">Cari</button>
                        </div>
                        <!-- Kontainer Peta Leaflet -->
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <!-- Tampilan Kanan: Ringkasan Analitik & Risk Scoring -->
            <div class="col-md-5 card-custom">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white fw-bold">Risk Scoring Engine Summary</div>
                    <div class="card-body text-center">
                        <h3 class="my-2">Status Risiko: <span id="riskLabel">-</span></h3>
                        <h1 class="display-3 fw-bold my-3 text-secondary" id="riskScore">0</h1>
                        <p class="text-muted">Kombinasi bobot Cuaca, Inflasi, Sentimen, & Kurs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Grafik Analitik -->
        <div class="row mt-4">
            <div class="col-md-6 card-custom">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white fw-bold">Risk History Trend</div>
                    <div class="card-body">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 card-custom">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white fw-bold">News Sentiment Analysis (AI Lexicon)</div>
                    <div class="card-body">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pustaka JavaScript ES6 & Visualisasi -->
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet.js (Peta) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js (Grafik) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Script AJAX akan kita taruh di bawah sini -->
    <script>
        // Logika JavaScript ES6 Fetch API akan kita susun di sini
    </script>
</body>
</html>