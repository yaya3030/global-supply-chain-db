<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Intelligence Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold text-success" href="#">📰 SUPPLY CHAIN NEWS INTELLIGENCE</a>
            <span class="navbar-text text-white-50">Stage 3: Event & Sentiment Analysis</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            <!-- Kolom Kiri: Visualisasi Sebaran Sentimen (Pie Chart) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-secondary mb-3">Distribusi Dampak Berita</h5>
                    <div class="d-flex align-items-center justify-content-center flex-grow-1">
                        <canvas id="sentimentPieChart" style="max-height: 260px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Umpan Live Intelijen Berita Logistik -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="card-title fw-bold text-dark mb-3">Feed Analisis Berita Global</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Topik Berita Logistik</th>
                                    <th>Sumber</th>
                                    <th class="text-center">Dampak Sistem</th>
                                </tr>
                            </thead>
                            <tbody id="news-table-body">
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Memilah indeks berita logistik via AJAX...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skrip AJAX untuk Fetch REST API & Menggambar Chart.js Pie + REALTIME POLLING -->
    <script>
        let sentimentPieChart = null;

        function fetchAndUpdateNews() {
            fetch('/api/news-intelligence')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        let rowsHtml = "";

                        // 1. Bangun List Tabel Berita
                        data.articles.forEach(article => {
                            rowsHtml += `
                                <tr>
                                    <td class="fw-semibold text-dark text-wrap" style="max-width: 400px;">${article.title}</td>
                                    <td class="text-secondary small font-monospace">${article.source}</td>
                                    <td class="text-center">
                                        <span class="badge bg-${article.badge_color} text-uppercase font-monospace p-2">
                                            ${article.impact_category}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });

                        const tbody = document.getElementById('news-table-body');
                        tbody.style.opacity = '0.7';
                        setTimeout(() => {
                            tbody.innerHTML = rowsHtml;
                            tbody.style.opacity = '1';
                        }, 100);

                        // 2. Update Pie Chart dengan sentiment distribution
                        const dist = data.sentiment_distribution;
                        if (!sentimentPieChart) {
                            const ctx = document.getElementById('sentimentPieChart').getContext('2d');
                            sentimentPieChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: ['Positive', 'Neutral', 'Disruption'],
                                    datasets: [{
                                        data: [dist.Positive, dist.Neutral, dist.Disruption],
                                        backgroundColor: [
                                            'rgba(25, 135, 84, 0.7)',
                                            'rgba(108, 117, 125, 0.7)',
                                            'rgba(220, 53, 69, 0.7)'
                                        ],
                                        borderColor: ['#fff', '#fff', '#fff'],
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: { position: 'bottom' }
                                    }
                                }
                            });
                        } else {
                            sentimentPieChart.data.datasets[0].data = [dist.Positive, dist.Neutral, dist.Disruption];
                            sentimentPieChart.update('active');
                        }
                    }
                })
                .catch(error => {
                    console.error("❌ News Intelligence Engine Error:", error);
                    document.getElementById('news-table-body').innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center text-danger py-4">Gagal memproses feed berita logistik.</td>
                        </tr>
                    `;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch immediately
            fetchAndUpdateNews();

            // Then setup realtime polling setiap 5 detik
            setInterval(fetchAndUpdateNews, 5000);
            
            console.log('✅ News Intelligence Dashboard - Realtime polling started (5s interval)');
        });
    </script>
</body>
</html>