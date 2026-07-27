@extends('layouts.app')

@section('title', 'Risk Scoring — Formula Analytics')
@section('breadcrumb', 'Risk Scoring')

@section('extra_head')
<style>
    /* Formula Banner */
    .formula-banner-card {
        background: linear-gradient(135deg, var(--violet-950) 0%, var(--violet-900) 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: var(--shadow-md);
        border: 1px solid rgba(236, 72, 153, 0.2);
        position: relative;
        overflow: hidden;
    }

    .formula-banner-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 180px;
        background: radial-gradient(ellipse at 30% 0%, rgba(236, 72, 153, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .formula-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        z-index: 1;
        margin-bottom: 20px;
    }

    .formula-title {
        font-size: 1.2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
        margin-bottom: 10px;
    }

    .formula-box {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        border-radius: 12px;
        padding: 14px 20px;
        font-size: 1.05rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fce7f3;
    }

    .formula-tag {
        background: rgba(255, 255, 255, 0.15);
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .country-selector-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    .country-selector-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--violet-200);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .country-selector-select {
        padding: 11px 16px;
        border-radius: 12px;
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.12);
        outline: none;
        min-width: 320px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .country-selector-select:focus {
        border-color: var(--violet-400);
        background: rgba(255, 255, 255, 0.18);
    }

    .country-selector-select option {
        background: var(--violet-950);
        color: #ffffff;
    }

    /* Selected Country Output Banner */
    .output-banner {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }

    .output-country-name {
        font-size: 2rem;
        font-weight: 900;
        color: var(--gray-900);
        letter-spacing: -0.5px;
    }

    .output-score-pill {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 10px 24px;
        border-radius: 9999px;
        font-size: 1.5rem;
        font-weight: 900;
        margin-top: 6px;
    }

    .output-score-pill.low {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 1.5px solid #6ee7b7;
    }

    .output-score-pill.medium {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1.5px solid #fbbf24;
    }

    .output-score-pill.high {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #7f1d1d;
        border: 1.5px solid #f87171;
    }

    .output-text-format {
        font-size: 1rem;
        color: var(--gray-500);
        margin-top: 4px;
        font-weight: 600;
        font-family: monospace;
    }

    /* 4 Component Mini Cards */
    .component-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 900px) {
        .component-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .comp-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .comp-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .comp-icon {
        font-size: 2rem;
        margin-bottom: 8px;
    }

    .comp-label {
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gray-500);
        margin-bottom: 8px;
    }

    .comp-score {
        font-size: 2.2rem;
        font-weight: 900;
        line-height: 1;
    }

    .comp-max {
        font-size: 0.8rem;
        color: var(--gray-400);
        margin-top: 4px;
        font-weight: 500;
    }

    .comp-card.weather .comp-score { color: #0284c7; }
    .comp-card.inflation .comp-score { color: #db2777; }
    .comp-card.exchange .comp-score { color: #15803d; }
    .comp-card.news .comp-score { color: #7c3aed; }

    /* Diagram Section */
    .diagram-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-md);
    }

    .diagram-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .diagram-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .diagram-title i {
        color: var(--violet-600);
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <!-- PAGE HEADER -->
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom: 20px;">
        <div>
            <h1 class="page-title"><i class="ti ti-shield-exclamation" style="color: var(--violet-600);"></i> Risk Scoring Engine</h1>
            <p class="page-subtitle">Kalkulasi Skor Risiko Rantai Pasok — Weather + Inflation + Exchange Rate + News Sentiment.</p>
        </div>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('report.pdf') }}" style="padding:10px 16px; font-size:13px; text-decoration:none; color:white; background:linear-gradient(135deg, var(--violet-600), var(--violet-700)); border-radius:8px; display:inline-flex; align-items:center; gap:6px; box-shadow:var(--shadow-violet); font-weight:600;">
                <i class="ti ti-file-type-pdf"></i> Download PDF
            </a>
            <a href="{{ route('report.excel') }}" style="padding:10px 16px; font-size:13px; text-decoration:none; color:white; background:linear-gradient(135deg, #10b981, #059669); border-radius:8px; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 10px rgba(16,185,129,0.3); font-weight:600;">
                <i class="ti ti-file-spreadsheet"></i> Download Excel
            </a>
        </div>
    </div>

    <!-- FORMULA BANNER + COUNTRY SELECTOR -->
    <div class="formula-banner-card animate-fade-up">
        <div class="formula-header-row">
            <!-- Formula Display -->
            <div>
                <div class="formula-title">
                    <i class="ti ti-math-symbols" style="color: var(--violet-400); font-size: 1.3rem;"></i> Formula Perhitungan Risk Score
                </div>
                <div class="formula-box">
                    <span style="color: #ffffff;">Risk Score =</span>
                    <span class="formula-tag">🌧️ Weather</span>
                    <span>+</span>
                    <span class="formula-tag">📈 Inflation</span>
                    <span>+</span>
                    <span class="formula-tag">💵 Exchange Rate</span>
                    <span>+</span>
                    <span class="formula-tag">📰 News Sentiment</span>
                </div>
            </div>

            <!-- Country Selector -->
            <div class="country-selector-group">
                <label class="country-selector-label">🔍 Pilih Negara</label>
                <select id="riskCountrySelect" class="country-selector-select" onchange="onCountryChange()">
                    <option value="">Memuat negara...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SELECTED COUNTRY OUTPUT BANNER -->
    <div class="output-banner animate-fade-up" id="selectedCountryOutput" style="display: none;">
        <div>
            <div class="output-country-name" id="out-country-name">—</div>
            <div class="output-text-format" id="out-formatted">—</div>
        </div>
        <div style="text-align: right;">
            <div class="output-score-pill" id="out-score-pill">
                <span id="out-score-num">—</span>
                <span id="out-score-level">—</span>
            </div>
            <div style="font-size: 0.82rem; color: var(--gray-400); margin-top: 8px;">Skor Risiko / 100</div>
        </div>
    </div>

    <!-- 4 COMPONENT BREAKDOWN CARDS -->
    <div class="component-grid" id="componentCardsGrid" style="display: none;">
        <div class="comp-card weather">
            <div class="comp-icon">🌧️</div>
            <div class="comp-label">Weather Risk</div>
            <div class="comp-score" id="comp-weather">—</div>
            <div class="comp-max">dari maks 25 poin</div>
        </div>
        <div class="comp-card inflation">
            <div class="comp-icon">📈</div>
            <div class="comp-label">Inflation Risk</div>
            <div class="comp-score" id="comp-inflation">—</div>
            <div class="comp-max">dari maks 25 poin</div>
        </div>
        <div class="comp-card exchange">
            <div class="comp-icon">💵</div>
            <div class="comp-label">Exchange Rate Risk</div>
            <div class="comp-score" id="comp-exchange">—</div>
            <div class="comp-max">dari maks 25 poin</div>
        </div>
        <div class="comp-card news">
            <div class="comp-icon">📰</div>
            <div class="comp-label">News Sentiment Risk</div>
            <div class="comp-score" id="comp-news">—</div>
            <div class="comp-max">dari maks 25 poin</div>
        </div>
    </div>

    <!-- SINGLE COUNTRY RISK DIAGRAM -->
    <div class="diagram-panel animate-fade-up" id="diagramPanel" style="display: none;">
        <div class="diagram-header">
            <span class="diagram-title">
                <i class="ti ti-chart-radar"></i> Risk Factor Breakdown — <span id="diagramCountryLabel">—</span>
            </span>
            <div style="display: flex; gap: 8px;">
                <button onclick="switchDiagram('bar')" id="btn-bar" class="cc-btn-primary" style="padding: 7px 14px; font-size: 0.82rem; border: none; background: linear-gradient(135deg, var(--violet-600), var(--violet-700)); color: white; border-radius: 8px; cursor: pointer; font-weight: 700;">Bar</button>
                <button onclick="switchDiagram('radar')" id="btn-radar" style="padding: 7px 14px; font-size: 0.82rem; border: 1.5px solid var(--gray-300); background: white; color: var(--gray-600); border-radius: 8px; cursor: pointer; font-weight: 700;">Radar</button>
            </div>
        </div>
        <div style="position: relative; height: 360px; max-width: 700px; margin: 0 auto;">
            <canvas id="riskBreakdownChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
let allRiskData = [];
let currentChart = null;
let currentDiagramType = 'bar';

document.addEventListener('DOMContentLoaded', function() {
    // Load all risk data once
    fetch('/api/risk-scoring')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                allRiskData = data.results;
                populateCountrySelect(allRiskData);

                // Default: Indonesia
                const defaultItem = allRiskData.find(c => c.country_name === 'Indonesia') || allRiskData[0];
                if (defaultItem) {
                    document.getElementById('riskCountrySelect').value = defaultItem.country_id;
                    renderCountryRisk(defaultItem);
                }
            }
        })
        .catch(err => {
            console.error("❌ Risk Scoring API Error:", err);
        });
});

function populateCountrySelect(list) {
    const sel = document.getElementById('riskCountrySelect');
    sel.innerHTML = list.map(c => `<option value="${c.country_id}">${c.country_name} (${c.iso2})</option>`).join('');
}

function onCountryChange() {
    const id = document.getElementById('riskCountrySelect').value;
    const item = allRiskData.find(c => String(c.country_id) === String(id));
    if (item) renderCountryRisk(item);
}

function renderCountryRisk(item) {
    // 1. Show output banner
    document.getElementById('selectedCountryOutput').style.display = 'flex';
    document.getElementById('out-country-name').innerText = item.country_name;
    document.getElementById('out-formatted').innerText = item.output_format;

    const pill = document.getElementById('out-score-pill');
    pill.className = 'output-score-pill';
    if (item.risk_level === 'Medium Risk') pill.classList.add('medium');
    else if (item.risk_level === 'High Risk') pill.classList.add('high');
    else pill.classList.add('low');

    document.getElementById('out-score-num').innerText = item.total_risk_score;
    document.getElementById('out-score-level').innerText = item.risk_level;

    // 2. Show component cards
    document.getElementById('componentCardsGrid').style.display = 'grid';
    document.getElementById('comp-weather').innerText = item.weather_risk;
    document.getElementById('comp-inflation').innerText = item.inflation_risk;
    document.getElementById('comp-exchange').innerText = item.exchange_rate_risk;
    document.getElementById('comp-news').innerText = item.news_sentiment_risk;

    // 3. Show diagram
    document.getElementById('diagramPanel').style.display = 'block';
    document.getElementById('diagramCountryLabel').innerText = item.country_name;

    renderDiagram(item, currentDiagramType);
}

function switchDiagram(type) {
    currentDiagramType = type;

    document.getElementById('btn-bar').style.background = type === 'bar' ? 'linear-gradient(135deg, var(--violet-600), var(--violet-700))' : 'white';
    document.getElementById('btn-bar').style.color = type === 'bar' ? 'white' : 'var(--gray-600)';
    document.getElementById('btn-bar').style.border = type === 'bar' ? 'none' : '1.5px solid var(--gray-300)';
    document.getElementById('btn-radar').style.background = type === 'radar' ? 'linear-gradient(135deg, var(--violet-600), var(--violet-700))' : 'white';
    document.getElementById('btn-radar').style.color = type === 'radar' ? 'white' : 'var(--gray-600)';
    document.getElementById('btn-radar').style.border = type === 'radar' ? 'none' : '1.5px solid var(--gray-300)';

    const id = document.getElementById('riskCountrySelect').value;
    const item = allRiskData.find(c => String(c.country_id) === String(id));
    if (item) renderDiagram(item, type);
}

function renderDiagram(item, type) {
    const labels = ['🌧️ Weather Risk', '📈 Inflation Risk', '💵 Exchange Rate Risk', '📰 News Sentiment Risk'];
    const values = [item.weather_risk, item.inflation_risk, item.exchange_rate_risk, item.news_sentiment_risk];

    const colors = [
        'rgba(2, 132, 199, 0.85)',
        'rgba(219, 39, 119, 0.85)',
        'rgba(21, 128, 61, 0.85)',
        'rgba(124, 58, 237, 0.85)'
    ];

    if (currentChart) {
        currentChart.destroy();
        currentChart = null;
    }

    const ctx = document.getElementById('riskBreakdownChart').getContext('2d');

    if (type === 'radar') {
        currentChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: `${item.country_name} Risk`,
                    data: values,
                    backgroundColor: 'rgba(219, 39, 119, 0.15)',
                    borderColor: 'rgba(219, 39, 119, 0.9)',
                    borderWidth: 2.5,
                    pointBackgroundColor: colors,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 25,
                        ticks: { stepSize: 5, font: { size: 11 }, color: '#6b7280' },
                        grid: { color: 'rgba(0,0,0,0.07)' },
                        angleLines: { color: 'rgba(0,0,0,0.07)' },
                        pointLabels: { font: { size: 13, weight: '700' }, color: '#374151' }
                    }
                }
            }
        });
    } else {
        currentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: `${item.country_name} Risk Score`,
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors.map(c => c.replace('0.85', '1')),
                    borderWidth: 1.5,
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 25,
                        ticks: { stepSize: 5, font: { size: 12, weight: '600' } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 12, weight: '700' }, color: '#374151' },
                        grid: { display: false }
                    }
                }
            }
        });
    }
}
</script>
@endsection