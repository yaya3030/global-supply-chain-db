<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Visualization Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <!-- Top Command Navbar -->
    <nav class="navbar navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold text-light" href="#">📊 SUPPLY CHAIN COMMAND CENTER</a>
            <span class="navbar-text text-white-50 font-monospace">Stage 3: Centralized Analytics Dashboard</span>
        </div>
    </nav>

    <div class="container mb-5">
        <!-- Seksi Atas: Row Summary Information Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white text-center">
                    <span class="text-muted small fw-bold text-uppercase">Cakupan Negara</span>
                    <h2 id="metric-countries" class="fw-bold text-primary my-1">...</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white text-center">
                    <span class="text-muted small fw-bold text-uppercase">Node Pelabuhan</span>
                    <h2 id="metric-ports" class="fw-bold text-success my-1">...</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white text-center">
                    <span class="text-muted small fw-bold text-uppercase">Indeks Efisiensi</span>
                    <h2 id="metric-efficiency" class="fw-bold text-info my-1">...</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white text-center">
                    <span class="text-muted small fw-bold text-uppercase">Insiden Aktif</span>
                    <h2 id="metric-disruptions" class="fw-bold text-danger my-1">...</h2>
                </div>
            </div>
        </div>

        <!-- Seksi Bawah: Grafik Visualisasi Tren -->
        <div class="row g-4">
            <!-- Grafik Kiri: Tren Efisiensi Logistik (Line Chart) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h6 class="fw-bold text-secondary mb-3">Tren Efisiensi Jalur Distribusi (%)</h6>
                    <canvas id="efficiencyChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
            <!-- Grafik Kanan: Tren Insiden Disrupsi (Bar Chart) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h6 class="fw-bold text-secondary mb-3">Frekuensi Kasus Disrupsi Logistik</h6>
                    <canvas id="disruptionChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip AJAX untuk Sinkronisasi Pusat Data & Pemicu Grafik -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/data-visualization-metrics')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        const data = res.payload;

                        // 1. Suntik Data Ringkasan ke Kartu Informasi
                        document.getElementById('metric-countries').innerText = data.summary.total_countries;
                        document.getElementById('metric-ports').innerText = data.summary.total_ports;
                        document.getElementById('metric-efficiency').innerText = data.summary.global_efficiency_score;
                        document.getElementById('metric-disruptions').innerText = data.summary.active_disruptions;

                        // 2. Render Grafik Tren Efisiensi (Line Chart)
                        const ctxLine = document.getElementById('efficiencyChart').getContext('2d');
                        new Chart(ctxLine, {
                            type: 'line',
                            data: {
                                labels: data.monthly_trends.labels,
                                datasets: [{
                                    label: 'Indeks Efisiensi',
                                    data: data.monthly_trends.efficiency_index,
                                    borderColor: '#0dcaf0',
                                    backgroundColor: 'rgba(13, 202, 240, 0.1)',
                                    fill: true,
                                    tension: 0.3
                                }]
                            },
                            options: { responsive: true }
                        });

                        // 3. Render Grafik Insiden Disrupsi (Bar Chart)
                        const ctxBar = document.getElementById('disruptionChart').getContext('2d');
                        new Chart(ctxBar, {
                            type: 'bar',
                            data: {
                                labels: data.monthly_trends.labels,
                                datasets: [{
                                    label: 'Jumlah Insiden',
                                    data: data.monthly_trends.disruption_incidents,
                                    backgroundColor: '#dc3545',
                                    borderRadius: 4
                                }]
                            },
                            options: { responsive: true }
                        });
                    }
                })
                .catch(error => console.error("Command Center Engine Error:", error));
        });
    </script>
</body>
</html>