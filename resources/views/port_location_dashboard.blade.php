<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port Location Dashboard</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet.js CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #portMap {
            height: 500px;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-dark bg-info mb-4 shadow">
        <div class="container">
            <a class="navbar-brand font-monospace fw-bold text-dark" href="#">🗺️ GLOBAL PORT GEOSPATIAL MAP</a>
            <span class="navbar-text text-dark-50 fw-semibold">Stage 3: Spatially Distributed Logistics Nodes</span>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            <!-- Kolom Utama: Peta Interaktif Leaflet.js -->
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-bold text-dark m-0">Peta Sebaran Node Pelabuhan Internasional</h5>
                        <span id="node-count" class="badge bg-dark p-2">Memuat Node Spasial...</span>
                    </div>
                    <!-- Kontainer untuk merender Peta -->
                    <div id="portMap" class="shadow-inner bg-light border"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet.js JS CDN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Skrip AJAX untuk Fetch REST API & Plotting Marker Peta -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Objek Peta Leaflet (Titik awal tengah dunia [lat, lng], zoom level 2)
            const map = L.map('portMap').setView([10.0, 20.0], 2);

            // 2. Set Layer Peta Menggunakan OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // 3. Tarik data koordinat dari REST API internal via AJAX Fetch
            fetch('/api/port-locations')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('node-count').innerText = `Terdeteksi: ${data.total_nodes} Pelabuhan`;

                        // Perulangan untuk meletakkan marker pin merah pada koordinat pelabuhan
                        data.results.forEach(port => {
                            if (port.latitude && port.longitude) {
                                const popupContent = `
                                    <div class="p-1">
                                        <h6 class="fw-bold text-primary mb-1">⚓ ${port.port_name}</h6>
                                        <p class="text-muted small mb-0">Negara: <b>${port.country_name}</b></p>
                                        <span class="text-secondary small font-monospace">Coord: ${port.latitude}, ${port.longitude}</span>
                                    </div>
                                `;

                                L.marker([port.latitude, port.longitude])
                                    .addTo(map)
                                    .bindPopup(popupContent);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error("Geospatial Map Engine Error:", error);
                    alert("Gagal memuat visualisasi koordinat spasial pelabuhan.");
                });
        });
    </script>
</body>
</html>