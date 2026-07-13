<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Country Comparison Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-secondary mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold text-white" href="#">📊 CROSS-COUNTRY PERFORMANCE ENGINE</a>
            <span class="navbar-text text-white-50">Stage 3: Benchmarking Supply Chain Hubs</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            <!-- Kolom Kiri: Visualisasi Chart Multi Bar Indikator -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-dark mb-3">Grafik Analisis Komparatif Negara</h5>
                    <div class="d-flex align-items-center justify-content-center flex-grow-1">
                        <canvas id="comparisonChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Tabel Berdampingan -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-secondary mb-3">Matriks Skor Indikator</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-start">Negara</th>
                                    <th>Efisiensi</th>
                                    <th>Risiko</th>
                                    <th>Stabilitas</th>
                                </tr>
                            </thead>
                            <tbody id="comparison-table-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Menghitung metrik perbandingan via AJAX...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip AJAX untuk Fetch REST API & Merender Multi-Bar Chart + REALTIME POLLING -->
    <script>
        let comparisonChart = null;

        function fetchAndUpdateComparison() {
            fetch('/api/country-comparison-data')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const labels = [];
                        const efficiencyScores = [];
                        const riskScores = [];
                        const stabilityScores = [];
                        let tableRowsHtml = "";

                        data.results.forEach(item => {
                            labels.push(item.country_name);
                            efficiencyScores.push(item.efficiency_score);
                            riskScores.push(item.risk_score);
                            stabilityScores.push(item.currency_stability);

                            tableRowsHtml += `
                                <tr>
                                    <td class="fw-bold text-start text-primary">🏳️ ${item.country_name} (${item.currency_code})</td>
                                    <td class="fw-semibold text-success">${item.efficiency_score}%</td>
                                    <td class="fw-semibold text-danger">${item.risk_score}%</td>
                                    <td class="fw-semibold text-info">${item.currency_stability}%</td>
                                </tr>
                            `;
                        });

                        const tbody = document.getElementById('comparison-table-body');
                        tbody.style.opacity = '0.7';
                        setTimeout(() => {
                            tbody.innerHTML = tableRowsHtml;
                            tbody.style.opacity = '1';
                        }, 100);

                        // Update atau buat chart
                        if (!comparisonChart) {
                            const ctx = document.getElementById('comparisonChart').getContext('2d');
                            comparisonChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        {
                                            label: 'Efisiensi Logistik',
                                            data: efficiencyScores,
                                            backgroundColor: 'rgba(25, 135, 84, 0.7)',
                                            borderColor: 'rgba(25, 135, 84, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Tingkat Risiko',
                                            data: riskScores,
                                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                            borderColor: 'rgba(220, 53, 69, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Stabilitas Mata Uang',
                                            data: stabilityScores,
                                            backgroundColor: 'rgba(13, 202, 240, 0.7)',
                                            borderColor: 'rgba(13, 202, 240, 1)',
                                            borderWidth: 1
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100
                                        }
                                    }
                                }
                            });
                        } else {
                            comparisonChart.data.labels = labels;
                            comparisonChart.data.datasets[0].data = efficiencyScores;
                            comparisonChart.data.datasets[1].data = riskScores;
                            comparisonChart.data.datasets[2].data = stabilityScores;
                            comparisonChart.update('active');
                        }
                    }
                })
                .catch(error => {
                    console.error("❌ Comparison Engine Error:", error);
                    document.getElementById('comparison-table-body').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-danger py-4">Gagal memproses analisis komparatif negara.</td>
                        </tr>
                    `;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch immediately
            fetchAndUpdateComparison();

            // Then setup realtime polling setiap 5 detik
            setInterval(fetchAndUpdateComparison, 5000);
            
            console.log('✅ Country Comparison Dashboard - Realtime polling started (5s interval)');
        });
    </script>
</body>
</html>