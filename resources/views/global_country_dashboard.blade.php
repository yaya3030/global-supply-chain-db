<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Country Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { bg-color: #f8f9fa; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace text-info" href="#">🚢 SUPPLY CHAIN ENGINE</a>
            <span class="navbar-text text-white-50">Stage 3: Global Country Analytics</span>
        </div>
    </nav>

    <div class="container mb-5">
        <!-- Top Statistics Row (Metrics Cards) -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card p-3 bg-white border-start border-info border-4">
                    <div class="text-muted small text-uppercase fw-bold">Total Terdaftar</div>
                    <div id="metric-countries" class="h3 fw-bold text-dark">- Negara</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 bg-white border-start border-success border-4">
                    <div class="text-muted small text-uppercase fw-bold">Total Infrastruktur Pelabuhan</div>
                    <div id="metric-ports" class="h3 fw-bold text-dark">- Titik Port</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Visualisasi Chart.js -->
            <div class="col-lg-6">
                <div class="card p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-secondary mb-3">Bar Chart: Distribusi Pelabuhan per Negara</h5>
                    <div class="chart-container flex-grow-1 d-flex align-items-center justify-content-center">
                        <canvas id="portsDistributionChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tabel Data Pecahan REST API -->
            <div class="col-lg-6">
                <div class="card p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-secondary mb-3">Data Inventori Negara</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Negara</th>
                                    <th class="text-center">ISO2</th>
                                    <th class="text-center">ISO3</th>
                                    <th class="text-center">Mata Uang</th>
                                    <th class="text-center">Aset Port</th>
                                </tr>
                            </thead>
                            <tbody id="country-table-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Memproses data via AJAX...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX Data Fetching & Chart Rendering Script Engine -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tembak endpoint REST API internal kita sendiri secara asinkronus
            fetch('/api/countries-summary')
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        // 1. Perbarui nilai komponen metrik ringkasan atas
                        document.getElementById('metric-countries').innerText = `${result.summary.total_countries} Negara`;
                        document.getElementById('metric-ports').innerText = `${result.summary.total_monitored_ports} Hub Maritim`;

                        // 2. Ekstraksi data mentah untuk kebutuhan grafik Chart.js
                        const labelNegara = [];
                        const dataJumlahPort = [];
                        let tableRowsHtml = "";

                        result.data.forEach(country => {
                            labelNegara.push(country.name);
                            dataJumlahPort.push(country.ports_count);

                            // Bangun baris baris tabel HTML secara dinamis
                            tableRowsHtml += `
                                <tr>
                                    <td class="fw-bold text-dark">${country.name}</td>
                                    <td class="text-center text-secondary small">${country.iso2 ?? '-'}</td>
                                    <td class="text-center text-secondary small">${country.iso3 ?? '-'}</td>
                                    <td class="text-center"><span class="badge bg-secondary font-monospace">${country.currency_code ?? '-'}</span></td>
                                    <td class="text-center fw-bold text-info">${country.ports_count}</td>
                                </tr>
                            `;
                        });

                        // Suntikkan kode HTML tabel ke DOM
                        document.getElementById('country-table-body').innerHTML = tableRowsHtml;

                        // 3. Render Chart.js Bar secara dinamis berdasarkan data database asli
                        const ctx = document.getElementById('portsDistributionChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labelNegara,
                                datasets: [{
                                    label: 'Jumlah Ketersediaan Pelabuhan',
                                    data: dataJumlahPort,
                                    backgroundColor: 'rgba(13, 202, 240, 0.6)',
                                    borderColor: 'rgba(13, 202, 240, 1)',
                                    borderWidth: 1.5,
                                    borderRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1 }
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error("Kesalahan AJAX:", error);
                    document.getElementById('country-table-body').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger py-4">Gagal memproses data dari REST API.</td>
                        </tr>
                    `;
                });
        });
    </script>
</body>
</html>