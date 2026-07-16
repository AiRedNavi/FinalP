<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Risk Intelligence Platform</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        #map { height: 440px; width: 100%; border-radius: 12px; z-index: 1; }
        .card { border: none; border-radius: 12px; transition: all 0.3s ease; }
        .card-header { border-bottom: none; border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; }
        .risk-display-box { background: #f8f9fa; border-radius: 16px; padding: 2rem 1rem; border: 1px dashed #dee2e6; }
        .form-select, .form-control, .btn { border-radius: 8px; padding: 0.6rem 1rem; }
        .badge-risk { font-size: 1.1rem; padding: 0.5em 1em; border-radius: 30px; }
    </style>
</head>
<body class="bg-light text-dark">

    <!-- Navbar Utama Modern -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
        <div class="container-fluid px-4">
            <span class="navbar-brand d-flex align-items-center fw-bold text-uppercase tracking-wider">
                <span class="me-2">🌐</span> Supply Chain Risk Platform
            </span>
            <span class="badge bg-secondary px-3 py-2 rounded-pill text-xs">Live Monitoring</span>
        </div>
    </nav>

    <div class="container-fluid my-4 px-4">
        
        <!-- Filter Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm p-3 bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="countrySelect" class="form-label fw-semibold text-secondary mb-1">Negara Pemantauan</label>
                            <select id="countrySelect" class="form-select border-secondary-subtle">
                                <!-- Data otomatis diisi oleh AJAX -->
                            </select>
                        </div>
                        <div class="col-md-8 text-md-end mt-3 mt-md-0 text-muted small">
                            Gunakan menu dropdown untuk menganalisis indeks risiko geopolitik, cuaca, inflasi, dan sentimen pasar secara real-time.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Panel -->
        <div class="row g-4">
            <!-- Tampilan Kiri: Geospatial Map -->
            <div class="col-lg-7">
                <div class="card shadow-sm h-100 bg-white">
                    <div class="card-header bg-primary text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span>📍 Port & Logistics Geospatial Map</span>
                    </div>
                    <div class="card-body p-3">
                        <!-- Input Cari Pelabuhan/Negara -->
                        <div class="input-group mb-3">
                            <input type="text" id="searchPort" class="form-control border-end-0" placeholder="Cari nama pelabuhan atau negara...">
                            <button class="btn btn-primary px-4" type="button" id="btnSearch">Cari</button>
                        </div>
                        <!-- Kontainer Peta Leaflet -->
                        <div id="map" class="shadow-inner"></div>
                    </div>
                </div>
            </div>

            <!-- Tampilan Kanan: Risk Scoring Engine -->
            <div class="col-lg-5">
                <div class="card shadow-sm h-100 bg-white">
                    <div class="card-header bg-dark text-white fw-bold py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>⚡ Risk Scoring Engine Summary</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                        <div class="risk-display-box my-auto">
                            <p class="text-uppercase tracking-wide fw-bold text-muted small mb-2">Status Risiko Saat Ini</p>
                            <div class="mb-3">
                                <span id="riskLabel" class="badge bg-secondary badge-risk">- Pilih Negara -</span>
                            </div>
                            <h1 class="display-1 fw-black my-2 text-dark" id="riskScore" style="font-weight: 800;">0</h1>
                            <p class="text-muted small px-3 mb-0">
                                Angka ini merupakan kalkulasi dinamis berbasis bobot parameter Cuaca, Inflasi, Sentimen AI Lexicon, & Stabilitas Kurs.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Grafik Analitik Berdampingan -->
        <div class="row g-4 mt-2">
            <!-- Grafik Tren -->
            <div class="col-md-6">
                <div class="card shadow-sm bg-white">
                    <div class="card-header bg-white border-bottom text-dark fw-bold py-3">
                        📈 Risk History Trend (Data Historis)
                    </div>
                    <div class="card-body" style="position: relative; height:300px;">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Grafik Sentimen -->
            <div class="col-md-6">
                <div class="card shadow-sm bg-white">
                    <div class="card-header bg-white border-bottom text-dark fw-bold py-3">
                        📊 News Sentiment Analysis (AI Lexicon Model)
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="position: relative; height:300px;">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // 1. Inisialisasi Peta Leaflet.js
        const map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let portMarkers = [];
        let riskChartInstance = null;
        let sentimentChartInstance = null;

        document.addEventListener("DOMContentLoaded", function() {
            loadCountries();
            loadPorts();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Event Listener Dropdown
            document.getElementById('countrySelect').addEventListener('change', function() {
                const countryId = this.value;
                if (!countryId) return;

                loadCountryRisk(countryId);
                loadCountryNews(countryId);
            });

            // Event Klik Tombol Cari Pelabuhan
            document.getElementById('btnSearch').addEventListener('click', function() {
                const query = document.getElementById('searchPort').value;
                loadPorts(query);
            });

            // Akselerasi UX: Tekan 'Enter' langsung melakukan pencarian port
            document.getElementById('searchPort').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('btnSearch').click();
                }
            });
        }

        /**
         * Mengambil daftar negara untuk dropdown
         */
        function loadCountries() {
            fetch('/api/countries')
                .then(response => {
                    if (!response.ok) throw new Error("Gagal memuat data API Negara");
                    return response.json();
                })
                .then(res => {
                    if (res.status === 'success') {
                        const select = document.getElementById('countrySelect');
                        select.innerHTML = '<option value="">-- Silahkan Pilih Negara Pemantauan --</option>';
                        res.data.forEach(country => {
                            select.innerHTML += `<option value="${country.id}">${country.name}</option>`;
                        });
                    }
                })
                .catch(err => console.error("Error loadCountries:", err));
        }

        /**
         * Mengambil & Mengalkulasi data risiko negara
         */
        function loadCountryRisk(countryId) {
            fetch(`/api/risk?country_id=${countryId}`)
                .then(response => {
                    if (!response.ok) throw new Error("Server bermasalah (Status 500/404)");
                    return response.json();
                })
                .then(res => {
                    if (res.status === 'success') {
                        const risk = res.current_risk.total_risk;
                        document.getElementById('riskScore').innerText = risk;
                        
                        const labelEl = document.getElementById('riskLabel');
                        // Reset kelas badge agar dinamis
                        labelEl.className = "badge badge-risk"; 
                        
                        if (risk < 30) {
                            labelEl.innerText = "Low Risk";
                            labelEl.classList.add("bg-success");
                        } else if (risk < 60) {
                            labelEl.innerText = "Medium Risk";
                            labelEl.classList.add("bg-warning", "text-dark");
                        } else {
                            labelEl.innerText = "High Risk";
                            labelEl.classList.add("bg-danger");
                        }

                        // Optimasi & Proteksi Bug Array Map
                        renderRiskChart(res.trends);
                    }
                })
                .catch(err => {
                    console.error("Error pada Engine Risiko:", err);
                    alert("Gagal menghitung risiko. Pastikan data seeder atau tabel database Anda sudah lengkap.");
                });
        }

        /**
         * Mengambil data berita & sentimen
         */
        function loadCountryNews(countryId) {
            fetch(`/api/news?country_id=${countryId}`)
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success' && res.data && res.data.length > 0) {
                        const latestNews = res.data[0];
                        renderSentimentChart(latestNews.positive_count, latestNews.negative_count);
                    } else {
                        renderSentimentChart(0, 0); // Tampilan Netral Default jika data kosong
                    }
                })
                .catch(err => {
                    console.error("Error pada Komponen Berita AI:", err);
                    renderSentimentChart(0, 0);
                });
        }

        /**
         * Render/Update Line Chart secara Aman (Defensive Code)
         */
        function renderRiskChart(trendsData) {
            const ctx = document.getElementById('riskChart').getContext('2d');
            
            if (riskChartInstance) riskChartInstance.destroy();

            // PROTEKSI UTAMA: Validasi apakah data berbentuk array utuh
            if (!Array.isArray(trendsData) || trendsData.length === 0) {
                // Gambar chart kosong dengan notifikasi jika data tren kosong
                riskChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: { labels: ['No Data'], datasets: [{ label: 'Global Risk Score Index', data: [0] }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
                return;
            }

            const labels = trendsData.map(t => new Date(t.created_at).toLocaleDateString());
            const scores = trendsData.map(t => t.total_risk);

            riskChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Global Risk Score Index',
                        data: scores,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.2,
                        borderWidth: 2
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } }
                }
            });
        }

        /**
         * Render/Update Doughnut Chart Sentimen
         */
        function renderSentimentChart(positive, negative) {
            const ctx = document.getElementById('sentimentChart').getContext('2d');
            const neutral = (positive === 0 && negative === 0) ? 100 : Math.max(0, 100 - (positive + negative));

            if (sentimentChartInstance) sentimentChartInstance.destroy();

            sentimentChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Positive Sentiment', 'Neutral', 'Negative Sentiment'],
                    datasets: [{
                        data: [positive, neutral, negative],
                        backgroundColor: ['#198754', '#6c757d', '#dc3545'],
                        borderWidth: 2
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        /**
         * Mengambil data titik koordinat pelabuhan
         */
        function loadPorts(searchQuery = '') {
            portMarkers.forEach(marker => map.removeLayer(marker));
            portMarkers = [];

            fetch(`/api/ports?search=${searchQuery}`)
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success' && Array.isArray(res.data)) {
                        res.data.forEach(port => {
                            if (port.latitude && port.longitude) {
                                const marker = L.marker([port.latitude, port.longitude])
                                    .addTo(map)
                                    .bindPopup(`<b>⚓ ${port.name}</b><br>Negara: ${port.country ? port.country.name : '-'}`);
                                portMarkers.push(marker);
                            }
                        });
                        
                        // Otomatis arahkan lensa kamera peta ke port yang dicari
                        if (res.data.length > 0 && searchQuery !== '') {
                            map.setView([res.data[0].latitude, res.data[0].longitude], 5);
                        }
                    }
                })
                .catch(err => console.error("Error loadPorts:", err));
        }
    </script>
</body>
</html>