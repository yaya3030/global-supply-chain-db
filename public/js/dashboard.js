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
    let isLoading = false;

    // 2. Inisialisasi Peta dengan optimasi smooth zoom
    function initMap() {
        // scrollWheelZoom: false agar peta tidak mengganggu saat user scroll halaman
        map = L.map('mapContainer', { 
            scrollWheelZoom: false,
            zoomControl: false,
            attributionControl: false 
        }).setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
    }

    function updateMap(country, coords) {
        const latlng = coords || fallbackCoords[country] || [0, 0];
        if (marker) map.removeLayer(marker);
        marker = L.marker(latlng, { 
            icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconSize: [35, 35],
                popupAnchor: [0, -15]
            })
        }).addTo(map);
        marker.bindPopup(`<strong>${country}</strong>`).openPopup();
        
        // Smooth map transition
        map.flyTo(latlng, 5, { duration: 0.8 });
    }

    // 3. Inisialisasi Grafik dengan animasi smooth
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
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#D4537E',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#993556',
                    borderWidth: 2.5,
                    borderCapStyle: 'round'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 750 },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    y: { 
                        grid: { 
                            color: 'rgba(244, 192, 209, 0.3)',
                            drawBorder: false
                        }, 
                        ticks: { color: '#993556', font: { size: 11 } },
                        beginAtZero: true
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#993556', font: { size: 11 } } 
                    }
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
            riskChart.update('active');
        }
    }

    // 4. Logika UI & Efek Visual - Enhanced dengan lebih banyak rain drops
    function renderRain(isRaining) {
        const layer = document.getElementById('rainLayer');
        layer.innerHTML = '';
        if (!isRaining) return;

        // Tambah lebih banyak rain drops untuk efek yang lebih indah
        const dropCount = 12;
        for (let i = 0; i < dropCount; i++) {
            const drop = document.createElement('div');
            drop.className = 'rain-drop';
            drop.style.left = (Math.random() * 100) + '%';
            drop.style.animationDuration = (0.6 + Math.random() * 0.5) + 's';
            drop.style.animationDelay = (Math.random() * 1.0) + 's';
            drop.style.opacity = (0.4 + Math.random() * 0.6);
            layer.appendChild(drop);
        }
    }

    function isRainCondition(weatherCode, description) {
        if (typeof weatherCode === 'number') {
            return (weatherCode >= 51 && weatherCode <= 67) || (weatherCode >= 80 && weatherCode <= 99);
        }
        if (typeof description === 'string') {
            return /hujan|rain|storm|badai|drizzle|precipitation/i.test(description);
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

        newsList.slice(0, 3).forEach((item, index) => {
            const sentimentClass = item.sentiment === 'positive' ? 'badge-positive'
                : item.sentiment === 'negative' ? 'badge-negative' : 'badge-neutral';
            
            const el = document.createElement('div');
            el.className = 'news-item';
            el.style.animationDelay = (index * 0.1) + 's';
            el.innerHTML = `<span>${item.title}</span><span class="news-badge ${sentimentClass}">${item.sentiment.charAt(0).toUpperCase()}</span>`;
            grid.appendChild(el);
        });
    }

    // 5. Loading state management
    function setLoading(loading) {
        isLoading = loading;
        const cards = document.querySelectorAll('.kpi-card');
        cards.forEach(card => {
            if (loading) {
                card.style.opacity = '0.6';
                card.style.pointerEvents = 'none';
            } else {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
            }
        });
    }

    // 6. Fungsi Utama Fetch Data dengan error handling lebih baik
    async function loadCountryData(country) {
        if (isLoading) return;
        
        currentCountry = country;
        setLoading(true);

        // Reset UI ke status loading
        document.getElementById('v-country-map').textContent = country;
        document.getElementById('v-country-chart').textContent = country;
        document.getElementById('v-country-news').textContent = country;
        ['population', 'gdp', 'inflation', 'currency', 'weather-temp', 'risk'].forEach(id => {
            const el = document.getElementById(`v-${id}`);
            if (el) el.textContent = '...';
        });

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            const res = await fetch(
                `${apiBase}/dashboard/country-data?country=${encodeURIComponent(country)}`,
                { signal: controller.signal }
            );
            
            clearTimeout(timeoutId);
            
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            // Update DOM dengan smooth animation
            document.getElementById('v-population').textContent = data.population ?? '-';
            document.getElementById('v-gdp').textContent = data.gdp ?? '-';
            document.getElementById('v-inflation').textContent = data.inflation ?? '-';
            
            const dirSymbol = data.currency?.direction === 'up' ? '▲' : '▼';
            const rate = data.currency?.display ?? '-';
            const change = data.currency?.rate_change_percent ?? '0';
            document.getElementById('v-currency').textContent = `${rate} ${dirSymbol} ${change}%`;
            // Simpan rate untuk update realtime
            window.currentCurrencyRate = data.currency?.rate ?? null;

            document.getElementById('v-weather-temp').textContent = (data.weather?.temp ?? '-') + '°C';
            document.getElementById('v-weather-cond').textContent = data.weather?.condition ?? '-';

            const raining = isRainCondition(data.weather?.code, data.weather?.condition);
            const weatherIcon = document.getElementById('weatherIcon');
            weatherIcon.className = 'ti weather-icon ' + (raining ? 'ti-cloud-rain' : 'ti-sun');
            renderRain(raining);

            document.getElementById('v-risk').textContent = `${data.risk?.score ?? '-'} (${data.risk?.level ?? '-'})`;

            // Update Peta & Chart dengan smooth transition
            updateMap(country, data.weather?.lat && data.weather?.lng ? [data.weather.lat, data.weather.lng] : null);
            updateChart(data.trend?.labels || [], data.trend?.values || []);
            renderNews(data.news || []);

        } catch (err) {
            console.error('Gagal memuat data:', err);
            document.getElementById('v-population').textContent = '-';
            document.getElementById('v-gdp').textContent = '-';
            document.getElementById('v-inflation').textContent = '-';
            document.getElementById('v-currency').textContent = '-';
            window.currentCurrencyRate = null;
            document.getElementById('v-weather-temp').textContent = '-';
            document.getElementById('v-risk').textContent = '-';
        } finally {
            setLoading(false);
        }
    }

    // 7. Event Listeners dengan smooth transitions
    document.querySelectorAll('.ctab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.ctab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const selectedCountry = this.dataset.country;

            // Sync state with realtime script to prevent it from polling the wrong country
            if (window.dashboardRealtime) {
                window.dashboardRealtime.currentCountry = selectedCountry;
            }
            
            // Add smooth transition
            const dashboardPage = document.querySelector('.dashboard-page');
            dashboardPage.style.opacity = '0.8';
            
            loadCountryData(selectedCountry);
            
            setTimeout(() => {
                dashboardPage.style.opacity = '1';
            }, 300);
        });
    });

    // Inisialisasi awal
    initMap();
    loadCountryData(currentCountry);
});