@extends('layouts.app')

@section('title', 'Data Visualization — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Data Visualization')

@section('extra_head')
<style>
    /* ===== TREND CHARTS SECTION ===== */
    .trends-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--gray-100);
    }
    .trends-section-title i {
        font-size: 1.25rem;
        color: var(--violet-500);
    }

    /* ===== TREND CARD ===== */
    .trend-card {
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: transform var(--transition-base), box-shadow var(--transition-base);
        border: 1px solid var(--gray-100);
    }
    .trend-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .trend-card-header {
        padding: 18px 22px 14px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid var(--gray-100);
    }
    .trend-card-left {
        display: flex;
        align-items: center;
        gap: 13px;
    }
    .trend-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .trend-icon.violet  { background: rgba(139,92,246,0.12); color: #8b5cf6; }
    .trend-icon.amber   { background: rgba(245,158,11,0.12);  color: #f59e0b; }
    .trend-icon.cyan    { background: rgba(6,182,212,0.12);   color: #06b6d4; }
    .trend-icon.red     { background: rgba(239,68,68,0.10);   color: #ef4444; }

    .trend-card-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 4px;
    }
    .trend-card-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--gray-900);
        line-height: 1;
    }
    .trend-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .trend-badge.up   { background: rgba(16,185,129,0.12); color: #059669; }
    .trend-badge.down { background: rgba(239,68,68,0.10);  color: #dc2626; }

    .trend-chart-wrap {
        padding: 4px 8px 12px;
        position: relative;
        height: 200px;
    }

    /* ===== CHART TABS ===== */
    .chart-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .chart-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all var(--transition-fast);
        background: var(--gray-100);
        color: var(--gray-600);
    }
    .chart-tab-btn:hover { background: var(--gray-200); }
    .chart-tab-btn.active-gdp      { background: rgba(139,92,246,0.12); color: #7c3aed; border-color: #8b5cf6; }
    .chart-tab-btn.active-inflation{ background: rgba(245,158,11,0.12);  color: #d97706; border-color: #f59e0b; }
    .chart-tab-btn.active-currency { background: rgba(6,182,212,0.12);   color: #0891b2; border-color: #06b6d4; }
    .chart-tab-btn.active-risk     { background: rgba(239,68,68,0.10);   color: #dc2626; border-color: #ef4444; }

    /* ===== COMBINED CHART ===== */
    .combined-card {
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-100);
        padding: 24px;
    }
    .combined-chart-wrap {
        position: relative;
        height: 320px;
        margin-top: 16px;
    }

    /* ===== GRID ===== */
    .trend-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media (max-width: 768px) {
        .trend-grid { grid-template-columns: 1fr; }
    }

    /* ===== LOADING STATE ===== */
    .chart-skeleton {
        background: linear-gradient(90deg, var(--gray-100) 25%, var(--gray-200) 50%, var(--gray-100) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 8px;
        height: 100%;
    }
    @keyframes shimmer {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }

    /* Stat Cards for Trends */
    .trend-stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    @media (max-width: 900px) {
        .trend-stat-row { grid-template-columns: repeat(2, 1fr); }
    }
    .ts-card {
        background: #fff;
        border-radius: var(--radius-md);
        border: 1px solid var(--gray-100);
        box-shadow: var(--shadow-sm);
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: box-shadow var(--transition-fast), transform var(--transition-fast);
    }
    .ts-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .ts-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ts-label { font-size: 0.76rem; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.04em; }
    .ts-value { font-size: 1.45rem; font-weight: 800; color: var(--gray-900); }
    .ts-badge { font-size: 0.72rem; font-weight: 700; padding: 3px 9px; border-radius: 999px; }
    .ts-badge.up   { background: rgba(16,185,129,0.1); color: #059669; }
    .ts-badge.down { background: rgba(239,68,68,0.1); color: #dc2626; }
    .ts-mini-bar {
        height: 4px;
        border-radius: 4px;
        margin-top: 2px;
        opacity: 0.35;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">

    {{-- PAGE HEADER --}}
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="page-title">Data Visualization</h1>
            <p class="page-subtitle">Economic & supply chain trend analytics — GDP, Inflation, Currency, Risk Index</p>
        </div>
        <div class="country-filter">
            <select id="countrySelect" class="form-select" style="min-width: 250px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200); padding: 10px 16px; font-weight: 500;">
                <option value="global">Global (World)</option>
                @if(isset($countries))
                    @foreach($countries as $c)
                        <option value="{{ strtolower($c->iso2 ?? '') }}">{{ $c->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    {{-- ===== STAT SUMMARY CARDS ===== --}}
    <div class="trend-stat-row stagger-children">
        <div class="ts-card animate-fade-up" id="ts-gdp">
            <div class="ts-card-top">
                <span class="ts-label"><i class="ti ti-chart-line" style="color:#8b5cf6;"></i> World GDP</span>
                <span class="ts-badge up" id="ts-gdp-badge">—</span>
            </div>
            <div class="ts-value" id="ts-gdp-val">...</div>
            <div class="ts-mini-bar" style="background:#8b5cf6;"></div>
        </div>
        <div class="ts-card animate-fade-up" id="ts-inflation" style="animation-delay:60ms;">
            <div class="ts-card-top">
                <span class="ts-label"><i class="ti ti-flame" style="color:#f59e0b;"></i> Inflation</span>
                <span class="ts-badge down" id="ts-inf-badge">—</span>
            </div>
            <div class="ts-value" id="ts-inf-val">...</div>
            <div class="ts-mini-bar" style="background:#f59e0b;"></div>
        </div>
        <div class="ts-card animate-fade-up" id="ts-currency" style="animation-delay:120ms;">
            <div class="ts-card-top">
                <span class="ts-label"><i class="ti ti-currency-dollar" style="color:#06b6d4;"></i> USD/IDR</span>
                <span class="ts-badge down" id="ts-cur-badge">—</span>
            </div>
            <div class="ts-value" id="ts-cur-val">...</div>
            <div class="ts-mini-bar" style="background:#06b6d4;"></div>
        </div>
        <div class="ts-card animate-fade-up" id="ts-risk" style="animation-delay:180ms;">
            <div class="ts-card-top">
                <span class="ts-label"><i class="ti ti-shield-exclamation" style="color:#ef4444;"></i> Risk Index</span>
                <span class="ts-badge down" id="ts-risk-badge">—</span>
            </div>
            <div class="ts-value" id="ts-risk-val">...</div>
            <div class="ts-mini-bar" style="background:#ef4444;"></div>
        </div>
    </div>

    {{-- ===== 4 INDIVIDUAL TREND CHARTS ===== --}}
    <div class="trends-section-title mb-3">
        <i class="ti ti-chart-area-line"></i>
        Economic Trend Overview — 10 Year History
    </div>

    <div class="trend-grid mb-4">

        {{-- GDP Trend --}}
        <div class="trend-card animate-fade-up">
            <div class="trend-card-header">
                <div class="trend-card-left">
                    <div class="trend-icon violet"><i class="ti ti-trending-up"></i></div>
                    <div>
                        <div class="trend-card-title">GDP Trend</div>
                        <div class="trend-card-value" id="gdp-current-val">...</div>
                    </div>
                </div>
                <span class="trend-badge up" id="gdp-badge">
                    <i class="ti ti-arrow-up"></i> <span id="gdp-change">...</span>
                </span>
            </div>
            <div class="trend-chart-wrap">
                <div class="chart-skeleton" id="gdp-skeleton"></div>
                <canvas id="gdpChart" style="display:none;"></canvas>
            </div>
        </div>

        {{-- Inflation Trend --}}
        <div class="trend-card animate-fade-up" style="animation-delay:80ms;">
            <div class="trend-card-header">
                <div class="trend-card-left">
                    <div class="trend-icon amber"><i class="ti ti-flame"></i></div>
                    <div>
                        <div class="trend-card-title">Inflation Trend</div>
                        <div class="trend-card-value" id="inf-current-val">...</div>
                    </div>
                </div>
                <span class="trend-badge down" id="inf-badge">
                    <i class="ti ti-arrow-down"></i> <span id="inf-change">...</span>
                </span>
            </div>
            <div class="trend-chart-wrap">
                <div class="chart-skeleton" id="inf-skeleton"></div>
                <canvas id="inflationChart" style="display:none;"></canvas>
            </div>
        </div>

        {{-- Currency Trend --}}
        <div class="trend-card animate-fade-up" style="animation-delay:160ms;">
            <div class="trend-card-header">
                <div class="trend-card-left">
                    <div class="trend-icon cyan"><i class="ti ti-currency-dollar"></i></div>
                    <div>
                        <div class="trend-card-title">Currency Trend (USD/IDR)</div>
                        <div class="trend-card-value" id="cur-current-val">...</div>
                    </div>
                </div>
                <span class="trend-badge down" id="cur-badge">
                    <i class="ti ti-arrow-down"></i> <span id="cur-change">...</span>
                </span>
            </div>
            <div class="trend-chart-wrap">
                <div class="chart-skeleton" id="cur-skeleton"></div>
                <canvas id="currencyChart" style="display:none;"></canvas>
            </div>
        </div>

        {{-- Risk Trend --}}
        <div class="trend-card animate-fade-up" style="animation-delay:240ms;">
            <div class="trend-card-header">
                <div class="trend-card-left">
                    <div class="trend-icon red"><i class="ti ti-shield-exclamation"></i></div>
                    <div>
                        <div class="trend-card-title">Risk Trend</div>
                        <div class="trend-card-value" id="risk-current-val">...</div>
                    </div>
                </div>
                <span class="trend-badge down" id="risk-badge">
                    <i class="ti ti-arrow-down"></i> <span id="risk-change">...</span>
                </span>
            </div>
            <div class="trend-chart-wrap">
                <div class="chart-skeleton" id="risk-skeleton"></div>
                <canvas id="riskChart" style="display:none;"></canvas>
            </div>
        </div>

    </div>

    {{-- ===== COMBINED MULTI-SERIES CHART ===== --}}
    <div class="combined-card animate-fade-up mb-4" style="animation-delay:300ms;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div class="trends-section-title" style="margin:0; border:none; padding:0;">
                <i class="ti ti-chart-dots-3"></i>
                Combined Trend Comparison
            </div>
            <div class="chart-tabs" id="combinedTabs">
                <button class="chart-tab-btn active-gdp"       data-series="gdp"      onclick="toggleSeries('gdp')">
                    <i class="ti ti-trending-up"></i> GDP
                </button>
                <button class="chart-tab-btn active-inflation" data-series="inflation" onclick="toggleSeries('inflation')">
                    <i class="ti ti-flame"></i> Inflation
                </button>
                <button class="chart-tab-btn active-currency"  data-series="currency"  onclick="toggleSeries('currency')">
                    <i class="ti ti-currency-dollar"></i> Currency
                </button>
                <button class="chart-tab-btn active-risk"      data-series="risk"      onclick="toggleSeries('risk')">
                    <i class="ti ti-shield-exclamation"></i> Risk
                </button>
            </div>
        </div>
        <div class="combined-chart-wrap">
            <div class="chart-skeleton" id="combined-skeleton"></div>
            <canvas id="combinedChart" style="display:none;"></canvas>
        </div>
    </div>

    {{-- ===== ORIGINAL EFFICIENCY & DISRUPTION CHARTS ===== --}}
    <div class="trends-section-title mb-3" style="margin-top: 8px;">
        <i class="ti ti-activity"></i>
        Supply Chain Performance Metrics
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="mb-4">
        <div class="card-modern animate-fade-up">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-line"></i> Distribution Efficiency Trend</span>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="efficiencyChart"></canvas>
            </div>
        </div>
        <div class="card-modern animate-fade-up" style="animation-delay: 80ms;">
            <div class="card-header-modern">
                <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Disruption Incident Frequency</span>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="disruptionChart"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@section('extra_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Chart.js global defaults ───────────────────────────────────
    Chart.defaults.font.family = "'Inter', sans-serif";

    const tooltipPlugin = {
        backgroundColor: 'rgba(15,23,42,0.85)',
        titleColor: '#f1f5f9',
        bodyColor: '#cbd5e1',
        padding: 12,
        cornerRadius: 10,
        borderColor: 'rgba(255,255,255,0.08)',
        borderWidth: 1,
        displayColors: true,
        boxWidth: 10,
        boxHeight: 10,
        boxPadding: 4,
        usePointStyle: true,
    };

    const scaleStyle = {
        y: { grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
    };

    function buildLineChart(canvasId, label, labels, data, color, colorAlpha, unit) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label,
                    data,
                    borderColor: color,
                    backgroundColor: colorAlpha,
                    fill: true,
                    tension: 0.45,
                    borderWidth: 2.5,
                    pointBackgroundColor: color,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 2,
                    pointHoverBorderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...tooltipPlugin,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} ${unit}`
                        }
                    }
                },
                scales: scaleStyle,
                animation: { duration: 900, easing: 'easeInOutQuart' }
            }
        });
    }

    // ─── Trend Charts State ──────────────────────────────────────────
    let combinedChart = null;
    let trendData = null;
    const seriesVisible = { gdp: true, inflation: true, currency: true, risk: true };
    
    // Store chart instances
    let gdpChartInstance = null;
    let infChartInstance = null;
    let curChartInstance = null;
    let riskChartInstance = null;

    // ─── Fetch Trends ────────────────────────────────────────────────
    function loadTrends(countryCode = 'global') {
        fetch(`/api/data-visualization-trends?country=${countryCode}`)
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return;
                trendData = res.payload;
                const labels = trendData.labels;

                // ── Stat Summary Cards
                const gdpS   = trendData.gdp.summary;
                const infS   = trendData.inflation.summary;
                const curS   = trendData.currency.summary;
                const riskS  = trendData.risk.summary;

                document.getElementById('ts-gdp-val').textContent     = gdpS.current;
                document.getElementById('ts-gdp-badge').textContent   = gdpS.change;
                document.getElementById('ts-inf-val').textContent     = infS.current;
                document.getElementById('ts-inf-badge').textContent   = infS.change;
                document.getElementById('ts-cur-val').textContent     = curS.current;
                document.getElementById('ts-cur-badge').textContent   = curS.change;
                document.getElementById('ts-risk-val').textContent    = riskS.current;
                document.getElementById('ts-risk-badge').textContent  = riskS.change;
                
                // Update badge colors dynamically based on trend
                const updateBadge = (id, trend) => {
                    const el = document.getElementById(id);
                    el.className = 'ts-badge ' + trend;
                };
                updateBadge('ts-gdp-badge', gdpS.trend);
                updateBadge('ts-inf-badge', infS.trend);
                updateBadge('ts-cur-badge', curS.trend);
                updateBadge('ts-risk-badge', riskS.trend);

                // ── GDP Chart
                document.getElementById('gdp-current-val').textContent = gdpS.current;
                document.getElementById('gdp-change').textContent       = gdpS.change;
                document.getElementById('gdp-badge').className          = 'trend-badge ' + gdpS.trend;
                document.getElementById('gdp-skeleton').style.display   = 'none';
                document.getElementById('gdpChart').style.display       = 'block';
                
                if (gdpChartInstance) {
                    gdpChartInstance.destroy();
                }
                gdpChartInstance = buildLineChart('gdpChart', trendData.gdp.label, labels, trendData.gdp.data,
                    trendData.gdp.color, trendData.gdp.color_alpha, trendData.gdp.unit);

                // ── Inflation Chart
                document.getElementById('inf-current-val').textContent = infS.current;
                document.getElementById('inf-change').textContent       = infS.change;
                document.getElementById('inf-badge').className          = 'trend-badge ' + infS.trend;
                document.getElementById('inf-skeleton').style.display   = 'none';
                document.getElementById('inflationChart').style.display = 'block';
                
                if (infChartInstance) {
                    infChartInstance.destroy();
                }
                infChartInstance = buildLineChart('inflationChart', trendData.inflation.label, labels, trendData.inflation.data,
                    trendData.inflation.color, trendData.inflation.color_alpha, trendData.inflation.unit);

                // ── Currency Chart
                document.getElementById('cur-current-val').textContent  = curS.current;
                document.getElementById('cur-change').textContent        = curS.change;
                document.getElementById('cur-badge').className           = 'trend-badge ' + curS.trend;
                document.getElementById('cur-skeleton').style.display    = 'none';
                document.getElementById('currencyChart').style.display   = 'block';
                
                if (curChartInstance) {
                    curChartInstance.destroy();
                }
                curChartInstance = buildLineChart('currencyChart', trendData.currency.label, labels, trendData.currency.data,
                    trendData.currency.color, trendData.currency.color_alpha, trendData.currency.unit);

                // ── Risk Chart
                document.getElementById('risk-current-val').textContent = riskS.current;
                document.getElementById('risk-change').textContent       = riskS.change;
                document.getElementById('risk-badge').className          = 'trend-badge ' + riskS.trend;
                document.getElementById('risk-skeleton').style.display   = 'none';
                document.getElementById('riskChart').style.display       = 'block';
                
                if (riskChartInstance) {
                    riskChartInstance.destroy();
                }
                riskChartInstance = buildLineChart('riskChart', trendData.risk.label, labels, trendData.risk.data,
                    trendData.risk.color, trendData.risk.color_alpha, trendData.risk.unit);

                // ── Combined Multi-series Chart
                buildCombinedChart(labels);
            })
            .catch(err => console.error('❌ Trend data error:', err));
    }
    
    // Initial Load
    loadTrends('global');
    
    // On Country Change
    document.getElementById('countrySelect').addEventListener('change', function(e) {
        loadTrends(e.target.value);
    });

    // ─── Combined Chart Builder ──────────────────────────────────────
    function buildCombinedChart(labels) {
        document.getElementById('combined-skeleton').style.display = 'none';
        document.getElementById('combinedChart').style.display     = 'block';

        const ctx = document.getElementById('combinedChart').getContext('2d');

        // Normalize data to 0-100 scale for unified display
        function normalize(arr) {
            const mn = Math.min(...arr), mx = Math.max(...arr);
            return arr.map(v => mx === mn ? 50 : Math.round((v - mn) / (mx - mn) * 100));
        }

        if (combinedChart) {
            combinedChart.destroy();
        }
        
        combinedChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: trendData.gdp.label + ' (normalized)',
                        data: normalize(trendData.gdp.data),
                        borderColor: trendData.gdp.color,
                        backgroundColor: trendData.gdp.color_alpha,
                        fill: false,
                        tension: 0.45,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: trendData.gdp.color,
                        hidden: !seriesVisible.gdp,
                        seriesKey: 'gdp'
                    },
                    {
                        label: trendData.inflation.label + ' (normalized)',
                        data: normalize(trendData.inflation.data),
                        borderColor: trendData.inflation.color,
                        backgroundColor: trendData.inflation.color_alpha,
                        fill: false,
                        tension: 0.45,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: trendData.inflation.color,
                        hidden: !seriesVisible.inflation,
                        seriesKey: 'inflation'
                    },
                    {
                        label: trendData.currency.label + ' (normalized)',
                        data: normalize(trendData.currency.data),
                        borderColor: trendData.currency.color,
                        backgroundColor: trendData.currency.color_alpha,
                        fill: false,
                        tension: 0.45,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: trendData.currency.color,
                        hidden: !seriesVisible.currency,
                        seriesKey: 'currency'
                    },
                    {
                        label: trendData.risk.label + ' (normalized)',
                        data: normalize(trendData.risk.data),
                        borderColor: trendData.risk.color,
                        backgroundColor: trendData.risk.color_alpha,
                        fill: false,
                        tension: 0.45,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: trendData.risk.color,
                        hidden: !seriesVisible.risk,
                        seriesKey: 'risk'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        ...tooltipPlugin,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}`
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0, max: 100,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => v }
                    },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
                },
                animation: { duration: 1000, easing: 'easeInOutQuart' }
            }
        });
    }

    // ─── Toggle series in combined chart ────────────────────────────
    window.toggleSeries = function(seriesKey) {
        if (!combinedChart) return;
        seriesVisible[seriesKey] = !seriesVisible[seriesKey];

        combinedChart.data.datasets.forEach(ds => {
            if (ds.seriesKey === seriesKey) {
                ds.hidden = !seriesVisible[seriesKey];
            }
        });
        combinedChart.update();

        // Update tab button style
        const btn = document.querySelector(`[data-series="${seriesKey}"]`);
        if (btn) {
            if (seriesVisible[seriesKey]) {
                btn.classList.add(`active-${seriesKey}`);
                btn.style.opacity = '1';
            } else {
                btn.classList.remove(`active-${seriesKey}`);
                btn.style.opacity = '0.45';
            }
        }
    };

    // ─── Efficiency & Disruption charts (original) ──────────────────
    fetch('/api/data-visualization-metrics')
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'success') return;
            const data = res.payload;

            const ctxLine = document.getElementById('efficiencyChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: data.monthly_trends.labels,
                    datasets: [{
                        label: 'Efficiency Index',
                        data: data.monthly_trends.efficiency_index,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { labels: { font: { size: 12, weight: 600 } } }, tooltip: tooltipPlugin },
                    scales: scaleStyle
                }
            });

            const ctxBar = document.getElementById('disruptionChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: data.monthly_trends.labels,
                    datasets: [{
                        label: 'Incident Count',
                        data: data.monthly_trends.disruption_incidents,
                        backgroundColor: 'rgba(239, 68, 68, 0.65)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1,
                        borderRadius: 7,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { labels: { font: { size: 12, weight: 600 } } }, tooltip: tooltipPlugin },
                    scales: { ...scaleStyle, y: { ...scaleStyle.y, beginAtZero: true } }
                }
            });
        })
        .catch(err => console.error('❌ Metrics error:', err));
});
</script>
@endsection