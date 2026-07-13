<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risk Scoring Engine</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light text-dark">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-danger mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold" href="#">🛡️ RISK SCORING ENGINE</a>
            <span class="navbar-text text-white-50">Stage 3: Supply Chain Risk Analytics</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            <!-- Kolom Kiri: Visualisasi Chart.js Line / Bar Comparison -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-danger mb-3">Grafik Perbandingan Skor Risiko Rantai Pasok</h5>
                    <div class="d-flex align-items-center justify-content-center flex-grow-1">
                        <canvas id="riskComparisonChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Tabel Matriks Risiko -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-secondary mb-3">Matriks Risiko Logistik Negara</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Negara</th>
                                    <th class="text-center">Infra</th>
                                    <th class="text-center">Finansial</th>
                                    <th class="text-center">Total Skor</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="risk-table-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Mengkalkulasi skor via AJAX Engine...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip AJAX untuk Menembak REST API Internal & Menggambar Grafik + REALTIME POLLING -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard-realtime-helper.js') }}"></script>
    <script>
        let riskComparisonChart = null;

        function fetchAndUpdateRiskData() {
            fetch('/api/risk-scoring')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const countriesLabels = [];
                        const scoresData = [];
                        let tableRowsHtml = "";

                        data.results.forEach(item => {
                            countriesLabels.push(item.country_name);
                            scoresData.push(item.total_risk_score);

                            // Penentuan warna badge status berdasarkan tingkat risiko
                            let badgeColor = "bg-success";
                            if (item.risk_level === 'Medium Risk') badgeColor = "bg-warning text-dark";
                            if (item.risk_level === 'High Risk') badgeColor = "bg-danger";

                            tableRowsHtml += `
                                <tr>
                                    <td class="fw-bold">${item.country_name} <span class="text-muted small">(${item.iso3})</span></td>
                                    <td class="text-center text-secondary">${item.infrastructure_risk}</td>
                                    <td class="text-center text-secondary">${item.financial_risk}</td>
                                    <td class="text-center fw-bold text-danger">${item.total_risk_score} / 100</td>
                                    <td class="text-center"><span class="badge ${badgeColor}">${item.risk_level}</span></td>
                                </tr>
                            `;
                        });

                        // Update table dengan smooth transition
                        const tbody = document.getElementById('risk-table-body');
                        tbody.style.opacity = '0.7';
                        setTimeout(() => {
                            tbody.innerHTML = tableRowsHtml;
                            tbody.style.opacity = '1';
                        }, 100);

                        // Update atau buat chart
                        if (!riskComparisonChart) {
                            const ctx = document.getElementById('riskComparisonChart').getContext('2d');
                            riskComparisonChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: countriesLabels,
                                    datasets: [{
                                        label: 'Skor Risiko Total',
                                        data: scoresData,
                                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                                        borderColor: 'rgba(220, 53, 69, 1)',
                                        borderWidth: 2,
                                        borderRadius: 5
                                    }]
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
                            // Update chart dengan smooth animation
                            riskComparisonChart.data.labels = countriesLabels;
                            riskComparisonChart.data.datasets[0].data = scoresData;
                            riskComparisonChart.update('active');
                        }
                    }
                })
                .catch(error => {
                    console.error("❌ Risk Scoring Error:", error);
                    const tbody = document.getElementById('risk-table-body');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger py-4">Gagal mengkalkulasi matriks risiko.</td>
                        </tr>
                    `;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch immediately
            fetchAndUpdateRiskData();

            // Then setup realtime polling setiap 5 detik
            setInterval(fetchAndUpdateRiskData, 5000);
            
            console.log('✅ Risk Scoring Dashboard - Realtime polling started (5s interval)');
        });
    </script>
</body>
</html>