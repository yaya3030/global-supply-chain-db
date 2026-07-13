<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Weather Monitoring</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-primary mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold" href="#">🌤️ MARITIME WEATHER MONITOR</a>
            <span class="navbar-text text-white-50">Stage 3: Supply Chain Weather Control</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-bold text-dark m-0">Live Status Cuaca Pelabuhan Internasional</h5>
                <span id="sync-time" class="badge bg-secondary p-2">Syncing...</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Pelabuhan</th>
                            <th>Negara</th>
                            <th class="text-center">Temperatur</th>
                            <th class="text-center">Kecepatan Angin</th>
                            <th class="text-center">Visibilitas</th>
                            <th>Kondisi Alami</th>
                            <th class="text-center">Pelayaran</th>
                        </tr>
                    </thead>
                    <tbody id="weather-table-body">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Memindai cuaca pelabuhan via AJAX Engine...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Skrip AJAX untuk Menembak REST API Internal & Mengisi Tabel Dinamis + REALTIME POLLING -->
    <script>
        function fetchAndUpdateWeather() {
            fetch('/api/global-weather-status')
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        document.getElementById('sync-time').innerText = `Terupdate: ${result.updated_at}`;
                        let rowsHtml = "";

                        result.data.forEach(item => {
                            // Penentuan warna badge keselamatan logistik
                            let statusBadge = "bg-success";
                            if (item.safety_status === 'Warning') statusBadge = "bg-warning text-dark animate-pulse";
                            if (item.safety_status === 'Alert') statusBadge = "bg-danger";

                            rowsHtml += `
                                <tr>
                                    <td class="fw-bold text-primary">⚓ ${item.port_name}</td>
                                    <td>${item.country_name}</td>
                                    <td class="text-center fw-semibold text-dark">${item.temperature}</td>
                                    <td class="text-center font-monospace">${item.wind_speed}</td>
                                    <td class="text-center text-secondary small">${item.visibility}</td>
                                    <td><span class="text-muted">${item.condition}</span></td>
                                    <td class="text-center"><span class="badge ${statusBadge} fw-bold">${item.safety_status}</span></td>
                                </tr>
                            `;
                        });

                        const tbody = document.getElementById('weather-table-body');
                        tbody.style.opacity = '0.7';
                        setTimeout(() => {
                            tbody.innerHTML = rowsHtml;
                            tbody.style.opacity = '1';
                        }, 100);
                    }
                })
                .catch(error => {
                    console.error("❌ Weather Engine Error:", error);
                    document.getElementById('weather-table-body').innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-danger py-4">Gagal memindai stasiun cuaca maritim global.</td>
                        </tr>
                    `;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch immediately
            fetchAndUpdateWeather();

            // Then setup realtime polling setiap 5 detik
            setInterval(fetchAndUpdateWeather, 5000);
            
            console.log('✅ Global Weather Dashboard - Realtime polling started (5s interval)');
        });
    </script>
</body>
</html>