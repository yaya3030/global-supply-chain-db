document.addEventListener('DOMContentLoaded', function () {
    // 1. Konfigurasi Awal
    const countries = window.DASHBOARD_COUNTRIES || [];
    const apiBase = window.DASHBOARD_API_BASE || '/api';
    
    // Koordinat fallback jika API tidak memberikan koordinat
    const fallbackCoords = {
        'Germany': [51.1657, 10.4515],
        'China': [35.8617, 104.1954],
        'Indonesia': [-0.7893, 113.9213],
        'Australia': [-25.2744, 133.7751]
    };

    let map, marker;
    let riskChart;
    let currentCountry = countries[0] || 'Germany';

    // 2. Inisialisasi Peta
    function initMap() {
        // scrollWheelZoom: false agar peta tidak mengganggu saat user scroll halaman
        map = L.map('mapContainer', { scrollWheelZoom: false }).setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
    }

    function updateMap(country, coords) {
        const latlng = coords || fallbackCoords[country] || [0, 0];
        if (marker) map.removeLayer(marker);
        marker = L.marker(latlng).addTo(map);
        marker.bindPopup(`<strong>${country}</strong>`).openPopup();
        map.setView(latlng, 5);
    }

    // 3. Inisialisasi Grafik
    function initChart(labels, data) {
        const ctx = document.getElementById('riskChart').getContext('2d');
        riskChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderColor: '#D4537E',
                    backgroundColor: 'rgba(212, 83, 126, 0.15)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#D4537E',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#F4C0D1' }, ticks: { color: '#993556', font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { color: '#993556', font: { size: 11 } } }
                }
            }
        });
    }

    function updateChart(labels, data) {
        if (!riskChart) {
            initChart(labels, data);
        } else {
            riskChart.data.labels = labels;
            riskChart.data.datasets[0].data = data;
            riskChart.update();
        }
    }

    // 4. Logika UI & Efek Visual
    function renderRain(isRaining) {
        const layer = document.getElementById('rainLayer');
        layer.innerHTML = '';
        if (!isRaining) return;

        for (let i = 0; i < 6; i++) {
            const drop = document.createElement('div');
            drop.className = 'rain-drop';
            drop.style.left = (10 + Math.random() * 80) + '%';
            drop.style.animationDuration = (0.5 + Math.random() * 0.4) + 's';
            drop.style.animationDelay = (Math.random() * 0.8) + 's';
            layer.appendChild(drop);
        }
    }

    function isRainCondition(weatherCode, description) {
        if (typeof weatherCode === 'number') {
            return (weatherCode >= 51 && weatherCode <= 67) || (weatherCode >= 80 && weatherCode <= 99);
        }
        if (typeof description === 'string') {
            return /hujan|rain|storm|badai/i.test(description);
        }
        return false;
    }

    function renderNews(newsList) {
        const grid = document.getElementById('newsGrid');
        grid.innerHTML = '';

        if (!newsList || newsList.length === 0) {
            grid.innerHTML = '<p style="color:#993556;font-size:12px;">Belum ada berita untuk negara ini.</p>';
            return;
        }

        newsList.slice(0, 3).forEach(item => {
            const sentimentClass = item.sentiment === 'positive' ? 'badge-positive'
                : item.sentiment === 'negative' ? 'badge-negative' : 'badge-neutral';
            
            const el = document.createElement('div');
            el.className = 'news-item';
            el.innerHTML = `<span>${item.title}</span><span class="news-badge ${sentimentClass}">${item.sentiment.charAt(0).toUpperCase()}</span>`;
            grid.appendChild(el);
        });
    }

    // 5. Fungsi Utama Fetch Data
    async function loadCountryData(country) {
        currentCountry = country;

        // Reset UI ke status loading
        document.getElementById('v-country-map').textContent = country;
        document.getElementById('v-country-chart').textContent = country;
        document.getElementById('v-country-news').textContent = country;
        ['population', 'gdp', 'inflation', 'currency', 'weather-temp', 'risk'].forEach(id => {
            document.getElementById(`v-${id}`).textContent = '...';
        });

        try {
            const res = await fetch(`${apiBase}/dashboard/country-data?country=${encodeURIComponent(country)}`);
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();

            // Update DOM
            document.getElementById('v-population').textContent = data.population ?? '-';
            document.getElementById('v-gdp').textContent = data.gdp ?? '-';
            document.getElementById('v-inflation').textContent = data.inflation ?? '-';
            
            const dirSymbol = data.currency?.direction === 'up' ? '▲' : '▼';
            document.getElementById('v-currency').textContent = `${dirSymbol} ${data.currency?.rate_change_percent ?? '-'}%`;

            document.getElementById('v-weather-temp').textContent = (data.weather?.temp ?? '-') + '°C';
            document.getElementById('v-weather-cond').textContent = data.weather?.condition ?? '-';

            const raining = isRainCondition(data.weather?.code, data.weather?.condition);
            document.getElementById('weatherIcon').className = 'ti weather-icon ' + (raining ? 'ti-cloud-rain' : 'ti-sun');
            renderRain(raining);

            document.getElementById('v-risk').textContent = `${data.risk?.score ?? '-'} (${data.risk?.level ?? '-'})`;

            // Update Peta & Chart
            updateMap(country, data.weather?.lat && data.weather?.lng ? [data.weather.lat, data.weather.lng] : null);
            updateChart(data.trend?.labels || [], data.trend?.values || []);
            renderNews(data.news || []);

        } catch (err) {
            console.error('Gagal memuat data:', err);
            alert('Gagal mengambil data. Pastikan API controller sudah aktif.');
        }
    }

    // 6. Event Listeners
    document.querySelectorAll('.ctab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.ctab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadCountryData(this.dataset.country);
        });
    });

    // Inisialisasi awal
    initMap();
    loadCountryData(currentCountry);
});