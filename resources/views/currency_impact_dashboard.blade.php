@extends('layouts.app')

@section('title', 'Currency Impact — Nilai Tukar & Grafik Perubahan Kurs')
@section('breadcrumb', 'Currency Impact')

@section('extra_head')
<style>
    /* Selector Card Header */
    .currency-selector-banner {
        background: linear-gradient(135deg, var(--violet-950) 0%, var(--violet-900) 100%);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        border: 1px solid rgba(236, 72, 153, 0.2);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .currency-selector-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 180px;
        background: radial-gradient(ellipse at 20% 0%, rgba(236, 72, 153, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }

    .banner-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 24px;
        position: relative;
        z-index: 1;
    }

    .banner-title {
        font-size: 1.4rem;
        font-weight: 900;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .banner-subtitle {
        font-size: 0.9rem;
        color: var(--violet-200);
        margin-top: 4px;
        font-weight: 500;
    }

    .currency-select-pill {
        padding: 11px 18px;
        border-radius: 12px;
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
        outline: none;
        min-width: 300px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .currency-select-pill:focus {
        border-color: var(--violet-400);
        background: rgba(255, 255, 255, 0.18);
    }

    .currency-select-pill option {
        background: var(--violet-950);
        color: #ffffff;
    }

    /* Rate Spotlight Card */
    .rate-spotlight {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 900px) {
        .rate-spotlight { grid-template-columns: repeat(2, 1fr); }
    }

    .rate-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 22px 20px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .rate-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .rate-card-label {
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-500);
    }

    .rate-card-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: var(--gray-900);
        line-height: 1;
        letter-spacing: -0.5px;
    }

    .rate-card-sub {
        font-size: 0.82rem;
        color: var(--gray-400);
        font-weight: 500;
    }

    .rate-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 4px;
    }

    .rate-card-icon.violet { background: #f3e8ff; color: #7c3aed; }
    .rate-card-icon.blue { background: #dbeafe; color: #2563eb; }
    .rate-card-icon.green { background: #dcfce7; color: #15803d; }
    .rate-card-icon.amber { background: #fef3c7; color: #b45309; }

    /* Chart Type Toggle Buttons */
    .chart-toggle-btn {
        padding: 7px 14px;
        font-size: 0.82rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .chart-toggle-btn.active {
        background: linear-gradient(135deg, var(--violet-600), var(--violet-700));
        color: white;
        border: none;
        box-shadow: var(--shadow-violet);
    }

    .chart-toggle-btn.inactive {
        background: white;
        color: var(--gray-600);
        border: 1.5px solid var(--gray-300);
    }

    /* Impact Table */
    .impact-badge-low {
        background: #dcfce7; color: #15803d;
        padding: 3px 10px; border-radius: 9999px;
        font-size: 0.78rem; font-weight: 700;
    }

    .impact-badge-moderate {
        background: #fef3c7; color: #b45309;
        padding: 3px 10px; border-radius: 9999px;
        font-size: 0.78rem; font-weight: 700;
    }

    .impact-badge-high {
        background: #fee2e2; color: #991b1b;
        padding: 3px 10px; border-radius: 9999px;
        font-size: 0.78rem; font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title"><i class="ti ti-currency-exchange" style="color: var(--violet-600);"></i> Currency Impact Analytics</h1>
        <p class="page-subtitle">Pantau nilai tukar mata uang dan grafik perubahan kurs terhadap USD secara visual.</p>
    </div>

    <!-- CURRENCY SELECTOR BANNER -->
    <div class="currency-selector-banner animate-fade-up">
        <div class="banner-inner">
            <div>
                <div class="banner-title">
                    <i class="ti ti-currency-dollar" style="color: var(--violet-400);"></i> Monitor Nilai Tukar Mata Uang
                </div>
                <div class="banner-subtitle">Pilih mata uang untuk melihat nilai tukar & grafik perubahan kurs 30 hari terakhir terhadap USD.</div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <label style="font-size: 0.82rem; font-weight: 700; color: var(--violet-200); text-transform: uppercase; letter-spacing: 0.5px;">🔍 Pilih Negara</label>
                <select id="currencySelect" class="currency-select-pill" onchange="onCurrencyChange()">
                    <option value="">Memuat daftar negara...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- RATE SPOTLIGHT CARDS -->
    <div class="rate-spotlight" id="rateSpotlightGrid" style="display: none;">
        <div class="rate-card">
            <div class="rate-card-icon violet"><i class="ti ti-currency-dollar"></i></div>
            <div class="rate-card-label">Kode Mata Uang</div>
            <div class="rate-card-value" id="spot-code" style="font-size: 2.2rem; color: var(--violet-700);">—</div>
            <div class="rate-card-sub">Terhadap USD</div>
        </div>
        <div class="rate-card">
            <div class="rate-card-icon blue"><i class="ti ti-arrows-exchange"></i></div>
            <div class="rate-card-label">Kurs Saat Ini</div>
            <div class="rate-card-value" id="spot-rate" style="color: #1d4ed8;">—</div>
            <div class="rate-card-sub" id="spot-rate-label">per 1 USD</div>
        </div>
        <div class="rate-card">
            <div class="rate-card-icon green"><i class="ti ti-trending-up"></i></div>
            <div class="rate-card-label">Kurs Tertinggi (30H)</div>
            <div class="rate-card-value" id="spot-high" style="color: #15803d;">—</div>
            <div class="rate-card-sub">Nilai tertinggi 30 hari</div>
        </div>
        <div class="rate-card">
            <div class="rate-card-icon amber"><i class="ti ti-trending-down"></i></div>
            <div class="rate-card-label">Kurs Terendah (30H)</div>
            <div class="rate-card-value" id="spot-low" style="color: #b45309;">—</div>
            <div class="rate-card-sub">Nilai terendah 30 hari</div>
        </div>
    </div>

    <!-- EXCHANGE RATE CHART -->
    <div class="card-modern animate-fade-up" id="chartPanel" style="display: none; margin-bottom: 24px;">
        <div class="card-header-modern">
            <span class="card-title-modern">
                <i class="ti ti-chart-line" style="color: var(--violet-600);"></i>
                Grafik Perubahan Kurs — <span id="chartCurrencyLabel">—</span> vs USD (30 Hari Terakhir)
            </span>
            <div style="display: flex; gap: 8px; margin-left: auto;">
                <button onclick="switchChart('line')" id="btn-line" class="chart-toggle-btn active">📈 Line</button>
                <button onclick="switchChart('bar')" id="btn-bar" class="chart-toggle-btn inactive">📊 Bar</button>
            </div>
        </div>
        <div style="position: relative; height: 360px;">
            <canvas id="exchangeRateChart"></canvas>
        </div>
    </div>

    <!-- IMPACT ANALYSIS TABLE -->
    <div class="card-modern animate-fade-up">
        <div class="card-header-modern" style="flex-wrap: wrap; gap: 12px;">
            <span class="card-title-modern">
                <i class="ti ti-table" style="color: var(--violet-600);"></i> Matriks Dampak Mata Uang — Supply Chain
            </span>
            <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
                <input type="text" id="currencyTableSearch" placeholder="🔍 Cari negara atau kode mata uang..." onkeyup="filterCurrencyTable()"
                    style="padding: 8px 14px; border: 1.5px solid var(--gray-300); border-radius: 10px; font-size: 13px; width: 280px; outline: none;">
            </div>
        </div>
        <div style="overflow-x: auto; max-height: 460px; overflow-y: auto;">
            <table class="table-modern" style="width: 100%;">
                <thead style="position: sticky; top: 0; z-index: 2; background: white;">
                    <tr>
                        <th>Negara</th>
                        <th style="text-align: center;">Mata Uang</th>
                        <th style="text-align: center;">Nilai Tukar (vs USD)</th>
                        <th style="text-align: center;">Risk Score</th>
                        <th style="text-align: center;">Biaya Tambahan</th>
                        <th style="text-align: center;">Dampak</th>
                    </tr>
                </thead>
                <tbody id="currency-table-body">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 32px; color: var(--gray-400);">
                            <div class="loading-skeleton" style="height: 16px; width: 200px; margin: 0 auto;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
let exchangeRateChart = null;
let currentChartType = 'line';
let currentCurrencyCode = 'IDR';
let currentHistoricalData = null;
let allImpactData = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load impact table first, then populate country dropdown from its data
    loadImpactTable();
});

// =====================================================================
// POPULATE COUNTRY DROPDOWN FROM IMPACT DATA
// =====================================================================
function populateCountrySelect(data) {
    const select = document.getElementById('currencySelect');
    // Build unique countries (deduplicate by currency code to keep diverse list)
    const seen = {};
    const options = data
        .filter(d => { if (seen[d.country_name]) return false; seen[d.country_name] = true; return true; })
        .map(d => `<option value="${d.currency_code}" data-country="${d.country_name}">${d.country_name}</option>`);
    select.innerHTML = options.join('');

    // Default: Indonesia
    const indonesia = data.find(d => d.country_name === 'Indonesia');
    if (indonesia) {
        select.value = indonesia.currency_code;
    } else {
        select.selectedIndex = 0;
    }

    // Trigger load
    onCurrencyChange();
}

// =====================================================================
// ON CURRENCY SELECT CHANGE
// =====================================================================
function onCurrencyChange() {
    const code = document.getElementById('currencySelect').value;
    if (!code) return;
    currentCurrencyCode = code;
    fetchHistoricalRates(code);
}

// =====================================================================
// FETCH HISTORICAL RATES & RENDER CHART + SPOTLIGHT CARDS
// =====================================================================
function fetchHistoricalRates(code) {
    fetch(`/api/currency-historical?code=${code}`)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                currentHistoricalData = res.data;

                // Show spotlight cards
                const maxRate = Math.max(...res.data.rates);
                const minRate = Math.min(...res.data.rates);
                const fmt = (n) => n >= 1000 ? n.toLocaleString('en-US', { maximumFractionDigits: 2 }) : n.toFixed(4);

                // Get country name from selected option
                const sel = document.getElementById('currencySelect');
                const selectedOption = sel.options[sel.selectedIndex];
                const countryName = selectedOption ? (selectedOption.getAttribute('data-country') || code) : code;

                document.getElementById('rateSpotlightGrid').style.display = 'grid';
                document.getElementById('spot-code').innerText = code;
                document.getElementById('spot-rate').innerText = fmt(res.data.current_rate);
                document.getElementById('spot-rate-label').innerText = `per 1 USD (${code})`;
                document.getElementById('spot-high').innerText = fmt(maxRate);
                document.getElementById('spot-low').innerText = fmt(minRate);

                // Show chart panel
                document.getElementById('chartPanel').style.display = 'block';
                document.getElementById('chartCurrencyLabel').innerText = `${countryName} (${code})`;

                renderChart(res.data, currentChartType);
            }
        })
        .catch(err => console.error("❌ Historical Rates Error:", err));
}

// =====================================================================
// RENDER CHART.JS CHART
// =====================================================================
function renderChart(data, type) {
    if (exchangeRateChart) {
        exchangeRateChart.destroy();
        exchangeRateChart = null;
    }

    const ctx = document.getElementById('exchangeRateChart').getContext('2d');
    const violetGradient = ctx.createLinearGradient(0, 0, 0, 360);
    violetGradient.addColorStop(0, 'rgba(219, 39, 119, 0.35)');
    violetGradient.addColorStop(1, 'rgba(219, 39, 119, 0.0)');

    if (type === 'line') {
        exchangeRateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: `${data.currency} per 1 USD`,
                    data: data.rates,
                    borderColor: 'rgba(219, 39, 119, 0.95)',
                    borderWidth: 2.5,
                    backgroundColor: violetGradient,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(219, 39, 119, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) {
                                const val = ctx.raw;
                                const fmt = val >= 1000
                                    ? val.toLocaleString('en-US', { maximumFractionDigits: 2 })
                                    : val.toFixed(4);
                                return ` ${data.currency}: ${fmt}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: { font: { size: 11, weight: '600' }, color: '#6b7280' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 11 }, maxTicksLimit: 10, color: '#9ca3af' },
                        grid: { display: false }
                    }
                }
            }
        });
    } else {
        exchangeRateChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: `${data.currency} per 1 USD`,
                    data: data.rates,
                    backgroundColor: data.rates.map((v, i) => {
                        const mid = data.rates[Math.floor(data.rates.length / 2)];
                        return v > mid ? 'rgba(219, 39, 119, 0.85)' : 'rgba(124, 58, 237, 0.85)';
                    }),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { font: { size: 11, weight: '600' } }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { ticks: { font: { size: 10 }, maxTicksLimit: 10, color: '#9ca3af' }, grid: { display: false } }
                }
            }
        });
    }
}

// =====================================================================
// SWITCH CHART TYPE (Line / Bar)
// =====================================================================
function switchChart(type) {
    currentChartType = type;

    document.getElementById('btn-line').className = type === 'line' ? 'chart-toggle-btn active' : 'chart-toggle-btn inactive';
    document.getElementById('btn-bar').className = type === 'bar' ? 'chart-toggle-btn active' : 'chart-toggle-btn inactive';

    if (currentHistoricalData) renderChart(currentHistoricalData, type);
}

// =====================================================================
// LOAD IMPACT ANALYSIS TABLE
// =====================================================================
function loadImpactTable() {
    fetch('/api/currency-impact-analysis')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                allImpactData = res.results;
                // Populate country dropdown first (which also triggers auto-select)
                populateCountrySelect(allImpactData);
                renderImpactTable(allImpactData);
            }
        })
        .catch(err => {
            console.error("❌ Currency Impact Error:", err);
            document.getElementById('currency-table-body').innerHTML = `
                <tr><td colspan="6" style="text-align:center; color:var(--danger); padding:24px;">Gagal memuat data dampak mata uang.</td></tr>
            `;
        });
}

function renderImpactTable(data) {
    const tbody = document.getElementById('currency-table-body');
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--gray-400); padding:24px;">Tidak ada data.</td></tr>`;
        return;
    }

    tbody.innerHTML = data.map(item => {
        let badgeClass = 'impact-badge-low';
        if (item.impact_level === 'Moderate Impact') badgeClass = 'impact-badge-moderate';
        if (item.impact_level === 'High Impact') badgeClass = 'impact-badge-high';

        const rateDisplay = item.exchange_rate_vs_usd !== 'N/A'
            ? parseFloat(item.exchange_rate_vs_usd) >= 1000
                ? parseFloat(item.exchange_rate_vs_usd).toLocaleString('en-US', { maximumFractionDigits: 2 })
                : item.exchange_rate_vs_usd
            : 'N/A';

        return `
            <tr style="cursor: pointer;" onclick="selectCurrencyFromTable('${item.currency_code}')" title="Klik untuk lihat grafik ${item.currency_code}">
                <td style="font-weight: 700; color: var(--gray-800);">🌐 ${item.country_name}</td>
                <td style="text-align: center;">
                    <span class="badge-modern badge-violet" style="font-family: monospace; font-weight: 700;">${item.currency_code}</span>
                </td>
                <td style="text-align: center; font-family: monospace; font-weight: 700; color: var(--violet-700);">${rateDisplay}</td>
                <td style="text-align: center; font-weight: 800; color: var(--gray-800);">${item.currency_risk_score} / 100</td>
                <td style="text-align: center; font-weight: 700; color: var(--danger);">+${item.cost_surge_estimate}</td>
                <td style="text-align: center;"><span class="${badgeClass}">${item.impact_level}</span></td>
            </tr>
        `;
    }).join('');
}

function selectCurrencyFromTable(code) {
    const select = document.getElementById('currencySelect');
    if (select) {
        select.value = code;
        if (select.value === code) {
            currentCurrencyCode = code;
            fetchHistoricalRates(code);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}

function filterCurrencyTable() {
    const q = document.getElementById('currencyTableSearch').value.toLowerCase();
    const filtered = allImpactData.filter(d =>
        d.country_name.toLowerCase().includes(q) || d.currency_code.toLowerCase().includes(q)
    );
    renderImpactTable(filtered);
}
</script>
@endsection