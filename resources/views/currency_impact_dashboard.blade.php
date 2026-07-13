<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Impact Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-warning mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold text-dark" href="#">💵 CURRENCY IMPACT ANALYTICS</a>
            <span class="navbar-text text-dark-50 fw-semibold">Stage 3: Financial Logistics Risk</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            <!-- Kolom Kiri: Visualisasi Chart.js Polar Area / Bar Chart -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-warning-emphasis mb-3">Grafik Indeks Risiko Mata Uang</h5>
                    <div class="d-flex align-items-center justify-content-center flex-grow-1">
                        <canvas id="currencyChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Tabel Analisis Finansial -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-secondary mb-3">Matriks Dampak Biaya Finansial</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Negara</th>
                                    <th class="text-center">Mata Uang</th>
                                    <th class="text-center">Skor Risiko</th>
                                    <th class="text-center">Estimasi Lonjakan Biaya</th>
                                    <th class="text-center">Dampak</th>
                                </tr>
                            </thead>
                            <tbody id="currency-table-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Menganalisis dampak finansial via AJAX...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip AJAX untuk Fetch REST API Internal & Menggambar Grafik + REALTIME POLLING -->
    <script>
        let currencyChart = null;

        function fetchAndUpdateCurrency() {
            fetch('/api/currency-impact-analysis')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const labels = [];
                        const riskScores = [];
                        let tableRowsHtml = "";

                        data.results.forEach(item => {
                            labels.push(item.country_name);
                            riskScores.push(item.currency_risk_score);

                            // Penentuan warna badge berdasarkan dampak volatilitas
                            let badgeColor = "bg-success";
                            if (item.impact_level === 'Moderate Impact') badgeColor = "bg-warning text-dark";
                            if (item.impact_level === 'High Impact') badgeColor = "bg-danger";

                            tableRowsHtml += `
                                <tr>
                                    <td class="fw-bold">${item.country_name}</td>
                                    <td class="text-center"><span class="badge bg-secondary font-monospace">${item.currency_code}</span></td>
                                    <td class="text-center fw-bold text-dark">${item.currency_risk_score} / 100</td>
                                    <td class="text-center fw-semibold text-danger">+${item.cost_surge_estimate}</td>
                                    <td class="text-center"><span class="badge ${badgeColor}">${item.impact_level}</span></td>
                                </tr>
                            `;
                        });

                        const tbody = document.getElementById('currency-table-body');
                        tbody.style.opacity = '0.7';
                        setTimeout(() => {
                            tbody.innerHTML = tableRowsHtml;
                            tbody.style.opacity = '1';
                        }, 100);

                        // Update atau buat chart
                        if (!currencyChart) {
                            const ctx = document.getElementById('currencyChart').getContext('2d');
                            currencyChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Skor Risiko Finansial',
                                        data: riskScores,
                                        backgroundColor: 'rgba(255, 193, 7, 0.6)',
                                        borderColor: 'rgba(255, 193, 7, 1)',
                                        borderWidth: 2,
                                        borderRadius: 5
                                    }]
                                },
                                options: {
                                    indexAxis: 'y',
                                    responsive: true,
                                    scales: {
                                        x: {
                                            beginAtZero: true,
                                            max: 100
                                        }
                                    }
                                }
                            });
                        } else {
                            currencyChart.data.labels = labels;
                            currencyChart.data.datasets[0].data = riskScores;
                            currencyChart.update('active');
                        }
                    }
                })
                .catch(error => {
                    console.error("❌ Currency Engine Error:", error);
                    document.getElementById('currency-table-body').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger py-4">Gagal memproses data dampak mata uang.</td>
                        </tr>
                    `;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch immediately
            fetchAndUpdateCurrency();

            // Then setup realtime polling setiap 5 detik
            setInterval(fetchAndUpdateCurrency, 5000);
            
            console.log('✅ Currency Impact Dashboard - Realtime polling started (5s interval)');
        });
    </script>
</body>
</html>