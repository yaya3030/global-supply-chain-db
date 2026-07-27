@extends('layouts.app')

@section('title', 'Global Countries — Analytics & Indicators')
@section('breadcrumb', 'Global Countries')

@section('extra_head')
<style>
    .metrics-showcase-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-md);
        margin-bottom: 24px;
    }

    .metrics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--gray-100);
        margin-bottom: 24px;
    }

    .metrics-title-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .metrics-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .country-select-box {
        padding: 10px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--gray-300);
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--gray-800);
        background: #ffffff;
        outline: none;
        min-width: 320px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .country-select-box:focus {
        border-color: var(--violet-500);
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
    }

    /* 5 Indicator Cards Grid */
    .indicator-5-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
    }

    @media (max-width: 1200px) {
        .indicator-5-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .indicator-5-grid {
            grid-template-columns: repeat(1, 1fr);
        }
    }

    .ind-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 22px;
        border: 1px solid var(--gray-200);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 150px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .ind-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .ind-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .ind-card-label {
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-500);
    }

    .ind-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    .ind-card-icon.gdp { background: #f3e8ff; color: #7c3aed; }
    .ind-card-icon.inflation { background: #fce7f3; color: #db2777; }
    .ind-card-icon.population { background: #dbeafe; color: #2563eb; }
    .ind-card-icon.currency { background: #dcfce7; color: #15803d; }
    .ind-card-icon.weather { background: #e0f2fe; color: #0284c7; }

    .ind-card-val {
        font-size: 1.35rem;
        font-weight: 800;
        margin-top: 12px;
        color: var(--gray-900);
        line-height: 1.2;
    }

    .ind-card-subtext {
        font-size: 0.8rem;
        color: var(--gray-500);
        margin-top: 10px;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title"><i class="ti ti-globe" style="color: var(--violet-600);"></i> Global Country Analytics</h1>
        <p class="page-subtitle">Sistem Informasi Indikator Negara: GDP, Inflasi, Populasi, Mata Uang, & Cuaca Real-time.</p>
    </div>

    <!-- FEATURED 5 INDICATORS SHOWCASE CARD -->
    <div class="metrics-showcase-card animate-fade-up">
        <div class="metrics-header">
            <div class="metrics-title-group">
                <span class="metrics-title" id="showcaseCountryTitle">
                    <i class="ti ti-chart-dots" style="color: var(--violet-600);"></i> Indikator Utama Negara
                </span>
                <span id="showcaseRegionBadge" class="badge-modern badge-violet" style="font-size: 0.82rem;">Global</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="metricsCountrySelect" style="font-weight: 700; font-size: 0.9rem; color: var(--gray-700);">🔍 Cari / Pilih Negara:</label>
                <select id="metricsCountrySelect" class="country-select-box" onchange="onMetricsCountrySelectChange()">
                    <option value="">Memuat daftar negara...</option>
                </select>
            </div>
        </div>

        <!-- 5 INDICATOR CARDS -->
        <div class="indicator-5-grid">
            <!-- 1. GDP -->
            <div class="ind-card">
                <div>
                    <div class="ind-card-top">
                        <span class="ind-card-label">GDP / PDB</span>
                        <div class="ind-card-icon gdp"><i class="ti ti-cash"></i></div>
                    </div>
                    <div class="ind-card-val" id="val-gdp" style="color: #7c3aed;">—</div>
                </div>
                <div class="ind-card-subtext">Produk Domestik Bruto</div>
            </div>

            <!-- 2. INFLASI -->
            <div class="ind-card">
                <div>
                    <div class="ind-card-top">
                        <span class="ind-card-label">Tingkat Inflasi</span>
                        <div class="ind-card-icon inflation"><i class="ti ti-trending-up"></i></div>
                    </div>
                    <div class="ind-card-val" id="val-inflation" style="color: #db2777;">—</div>
                </div>
                <div class="ind-card-subtext">Laju Inflasi Tahunan</div>
            </div>

            <!-- 3. POPULASI -->
            <div class="ind-card">
                <div>
                    <div class="ind-card-top">
                        <span class="ind-card-label">Populasi</span>
                        <div class="ind-card-icon population"><i class="ti ti-users"></i></div>
                    </div>
                    <div class="ind-card-val" id="val-population" style="color: #2563eb;">—</div>
                </div>
                <div class="ind-card-subtext">Total Jumlah Penduduk</div>
            </div>

            <!-- 4. MATA UANG -->
            <div class="ind-card">
                <div>
                    <div class="ind-card-top">
                        <span class="ind-card-label">Mata Uang</span>
                        <div class="ind-card-icon currency"><i class="ti ti-currency-dollar"></i></div>
                    </div>
                    <div class="ind-card-val" id="val-currency" style="color: #15803d;">—</div>
                </div>
                <div class="ind-card-subtext">Kode & Nama Valuta</div>
            </div>

            <!-- 5. CUACA SAAT INI -->
            <div class="ind-card">
                <div>
                    <div class="ind-card-top">
                        <span class="ind-card-label">Cuaca Saat Ini</span>
                        <div class="ind-card-icon weather"><i class="ti ti-cloud-storm"></i></div>
                    </div>
                    <div class="ind-card-val" id="val-weather" style="color: #0284c7;">—</div>
                </div>
                <div class="ind-card-subtext" id="val-weather-sub">Kondisi Atmosferik</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
var allCountries = [];

document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/countries-summary')
        .then(function(r) { return r.json(); })
        .then(function(result) {
            if (result.status === 'success') {
                allCountries = result.data;

                // Populate Country Selector Dropdown
                populateMetricsCountrySelect(allCountries);

                // Load default country indicators (Indonesia or first)
                const defaultCountry = allCountries.find(c => c.name.toLowerCase().includes('indonesia')) || allCountries[0];
                if (defaultCountry) {
                    selectCountryForMetrics(defaultCountry.id);
                }
            }
        })
        .catch(function(e) {
            console.error("❌ Countries Summary API Error:", e);
        });

    // Mulai polling real-time setiap 5 detik
    setInterval(() => {
        const selectedId = document.getElementById('metricsCountrySelect').value;
        if (selectedId) {
            fetchAndRenderCountryMetrics(selectedId);
        }
    }, 5000);
});

function populateMetricsCountrySelect(countries) {
    const select = document.getElementById('metricsCountrySelect');
    select.innerHTML = countries.map(c => `<option value="${c.id}">${c.name} (${c.iso2})</option>`).join('');
}

function onMetricsCountrySelectChange() {
    const id = document.getElementById('metricsCountrySelect').value;
    if (id) fetchAndRenderCountryMetrics(id);
}

function selectCountryForMetrics(countryId) {
    document.getElementById('metricsCountrySelect').value = countryId;
    fetchAndRenderCountryMetrics(countryId);
}

function fetchAndRenderCountryMetrics(countryId) {
    fetch(`/api/country-metrics?country_id=${countryId}`)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                document.getElementById('showcaseCountryTitle').innerHTML = `<i class="ti ti-chart-dots" style="color: var(--violet-600);"></i> ${data.country_name} (${data.iso2})`;
                document.getElementById('showcaseRegionBadge').innerText = data.region;

                document.getElementById('val-gdp').innerText = data.gdp;
                document.getElementById('val-inflation').innerText = data.inflation;
                document.getElementById('val-population').innerText = data.population;
                document.getElementById('val-currency').innerText = data.currency;

                // Weather formatting
                let weatherEmoji = '☀️';
                if (data.weather.weather_type === 'rain') weatherEmoji = '🌧️';
                if (data.weather.weather_type === 'storm') weatherEmoji = '⚡';
                if (data.weather.weather_type === 'strong_wind') weatherEmoji = '💨';

                document.getElementById('val-weather').innerText = `${weatherEmoji} ${data.weather.condition}`;
                document.getElementById('val-weather-sub').innerText = `${data.weather.temperature} • Angin ${data.weather.wind_speed}`;
            }
        })
        .catch(err => {
            console.error("❌ Country Metrics API Error:", err);
        });
}
</script>
@endsection