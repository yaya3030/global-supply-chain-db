@extends('layouts.app')

@section('title', 'Dynamic Country Comparison — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Country Compare')

@section('extra_head')
<style>
    /* ===== COMPARISON DASHBOARD STYLES ===== */
    .vs-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 32px;
        margin-bottom: 40px;
        background: linear-gradient(135deg, var(--gray-800), var(--gray-900));
        padding: 40px;
        border-radius: var(--radius-2xl);
        box-shadow: var(--shadow-xl);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .vs-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        pointer-events: none;
    }

    .vs-country {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        z-index: 1;
        min-width: 250px;
    }

    .vs-flag {
        width: 100px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: 2px solid rgba(255,255,255,0.1);
        transition: transform 0.3s ease;
    }

    .vs-select {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        padding: 10px 16px;
        border-radius: var(--radius-md);
        font-size: 1.1rem;
        font-weight: 700;
        backdrop-filter: blur(8px);
        width: 100%;
        text-align: center;
        cursor: pointer;
        outline: none;
    }
    
    .vs-select option {
        background: var(--gray-800);
        color: white;
    }

    .vs-badge {
        background: rgba(255,255,255,0.1);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }

    .vs-divider {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--violet-400);
        font-style: italic;
        text-shadow: 0 0 20px rgba(167, 139, 250, 0.4);
        z-index: 1;
    }

    .comparison-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .metric-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-100);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
    }

    .metric-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--gray-50);
        padding-bottom: 12px;
    }

    .metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .metric-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--gray-800);
    }

    .metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding: 12px;
        border-radius: 8px;
        background: var(--gray-50);
    }

    .metric-row.winner {
        background: var(--success-bg);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .metric-country {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--gray-700);
        max-width: 40%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .metric-value {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--gray-900);
        text-align: right;
    }

    .metric-row.winner .metric-value,
    .metric-row.winner .metric-country {
        color: var(--success);
    }

    .chart-container {
        background: white;
        border-radius: var(--radius-xl);
        padding: 32px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--gray-100);
        margin-bottom: 40px;
    }

    /* specific icons */
    .icon-info { background: rgba(59,130,246,0.1); color: var(--blue-600); }
    .icon-gdp { background: rgba(139,92,246,0.1); color: var(--violet-600); }
    .icon-inflation { background: rgba(245,158,11,0.1); color: var(--warning); }
    .icon-risk { background: rgba(239,68,68,0.1); color: var(--danger); }
    .icon-weather { background: rgba(16,185,129,0.1); color: var(--success); }
    .icon-currency { background: rgba(14,165,233,0.1); color: var(--cyan-600); }
    .icon-efficiency { background: rgba(16,185,129,0.1); color: var(--emerald-600); }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    
    <!-- VS HEADER -->
    <div class="vs-header animate-fade-up">
        <div class="vs-country">
            <img src="https://flagcdn.com/w160/de.png" alt="Country 1" id="flag1" class="vs-flag">
            <select id="country1" class="vs-select">
                @foreach($countries as $c)
                    <option value="{{ $c->iso2 }}" {{ $c->iso2 == 'DE' ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="vs-badge" id="badge1">EUR • Europe</div>
        </div>
        <div class="vs-divider">VS</div>
        <div class="vs-country">
            <img src="https://flagcdn.com/w160/au.png" alt="Country 2" id="flag2" class="vs-flag">
            <select id="country2" class="vs-select">
                @foreach($countries as $c)
                    <option value="{{ $c->iso2 }}" {{ $c->iso2 == 'AU' ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <div class="vs-badge" id="badge2">AUD • Oceania</div>
        </div>
    </div>

    <!-- RADAR CHART -->
    <div class="chart-container animate-fade-up" style="animation-delay: 100ms;">
        <h3 style="margin-bottom: 24px; font-weight: 800; text-align: center; color: var(--gray-800);">Multidimensional Index Comparison</h3>
        <div style="position: relative; height: 400px; width: 100%; display: flex; justify-content: center;">
            <canvas id="radarChart"></canvas>
        </div>
    </div>

    <!-- METRICS GRID -->
    <div class="comparison-grid" id="metrics-grid">
        <!-- JS will populate this -->
    </div>

</div>
@endsection

@section('extra_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        let radarChartInstance = null;

        function loadComparison(c1, c2) {
            // Update flags immediately
            document.getElementById('flag1').src = `https://flagcdn.com/w160/${c1.toLowerCase()}.png`;
            document.getElementById('flag2').src = `https://flagcdn.com/w160/${c2.toLowerCase()}.png`;

            fetch(`/api/country-comparison-data?country1=${c1}&country2=${c2}`)
                .then(res => res.json())
                .then(response => {
                    if(response.status !== 'success') return;
                    
                    const data = response.results;
                    const c1Data = data.countries[0];
                    const c2Data = data.countries[1];

                    // Update badges
                    document.getElementById('badge1').textContent = c1Data.currency;
                    document.getElementById('badge2').textContent = c2Data.currency;

                    const grid = document.getElementById('metrics-grid');
                    grid.innerHTML = ''; // clear existing
                    
                    // The 9 metrics requested by user
                    // Ibu kota, Populasi, Mata uang, GDP, Inflasi, Nilai tukar, Suhu rata-rata, Skor risiko, Nilai pasok
                    const metricProps = [
                        { key: 'capital', name: 'Ibu Kota', icon: 'ti-building-monument', bg: 'icon-info', type: 'string' },
                        { key: 'population', name: 'Populasi', icon: 'ti-users', bg: 'icon-info', type: 'string' },
                        { key: 'currency', name: 'Mata Uang', icon: 'ti-coin', bg: 'icon-info', type: 'string' },
                        
                        { key: 'gdp', name: 'GDP', icon: 'ti-chart-line', bg: 'icon-gdp', unit: ' Triliun USD', type: 'number', higherIsBetter: true },
                        { key: 'inflation', name: 'Inflasi', icon: 'ti-flame', bg: 'icon-inflation', unit: '%', type: 'number', higherIsBetter: false },
                        { key: 'exchange', name: 'Nilai Tukar (vs USD)', icon: 'ti-currency-dollar', bg: 'icon-currency', unit: '', type: 'number', higherIsBetter: false },
                        { key: 'weather', name: 'Suhu Rata-rata', icon: 'ti-sun', bg: 'icon-weather', unit: '°C (Indeks)', type: 'number', higherIsBetter: false },
                        { key: 'risk', name: 'Skor Risiko', icon: 'ti-shield-exclamation', bg: 'icon-risk', unit: ' / 100', type: 'number', higherIsBetter: false },
                        { key: 'efficiency', name: 'Nilai Pasok (Efficiency)', icon: 'ti-truck-delivery', bg: 'icon-efficiency', unit: ' / 100', type: 'number', higherIsBetter: true }
                    ];

                    // Build Metric Cards
                    metricProps.forEach((prop, index) => {
                        // Numeric data is inside .data, strings are on the root object
                        const val1 = prop.type === 'string' ? c1Data[prop.key] : c1Data.data[prop.key];
                        const val2 = prop.type === 'string' ? c2Data[prop.key] : c2Data.data[prop.key];
                        
                        let w1 = false, w2 = false;
                        
                        if (prop.type === 'number' && val1 !== val2) {
                            if (prop.higherIsBetter) {
                                w1 = val1 > val2;
                                w2 = val2 > val1;
                            } else {
                                w1 = val1 < val2;
                                w2 = val2 < val1;
                            }
                        }

                        const card = document.createElement('div');
                        card.className = `metric-card animate-fade-up`;
                        card.style.animationDelay = `${(index * 40)}ms`;
                        
                        card.innerHTML = `
                            <div class="metric-header">
                                <div class="metric-icon ${prop.bg}"><i class="ti ${prop.icon}"></i></div>
                                <div class="metric-title">${prop.name}</div>
                            </div>
                            <div class="metric-row ${w1 ? 'winner' : ''}">
                                <div class="metric-country">
                                    <img src="https://flagcdn.com/w20/${c1.toLowerCase()}.png" width="20" style="border-radius:2px;"> ${c1Data.name}
                                </div>
                                <div class="metric-value">${val1}${prop.unit || ''}</div>
                            </div>
                            <div class="metric-row ${w2 ? 'winner' : ''}">
                                <div class="metric-country">
                                    <img src="https://flagcdn.com/w20/${c2.toLowerCase()}.png" width="20" style="border-radius:2px;"> ${c2Data.name}
                                </div>
                                <div class="metric-value">${val2}${prop.unit || ''}</div>
                            </div>
                        `;
                        grid.appendChild(card);
                    });

                    // Build Radar Chart for numeric indicators
                    const normalize = (val, max, invert = false) => {
                        let n = (val / max) * 100;
                        if(invert) n = 100 - n; 
                        return Math.max(0, Math.min(100, n));
                    };

                    // [GDP(5T), Inflasi(10%), Risiko(100), Cuaca(15), Exchange(100), Efficiency(100)]
                    const d1 = c1Data.data;
                    const d2 = c2Data.data;

                    const c1Normalized = [
                        normalize(d1.gdp, 5),
                        normalize(d1.inflation, 10, true),
                        normalize(d1.risk, 100, true),
                        normalize(d1.weather, 15, true),
                        normalize(d1.exchange, 100, true), // Lower exchange means stronger currency vs USD generally, just an assumption for visualization
                        normalize(d1.efficiency, 100)
                    ];

                    const c2Normalized = [
                        normalize(d2.gdp, 5),
                        normalize(d2.inflation, 10, true),
                        normalize(d2.risk, 100, true),
                        normalize(d2.weather, 15, true),
                        normalize(d2.exchange, 100, true),
                        normalize(d2.efficiency, 100)
                    ];

                    const ctx = document.getElementById('radarChart').getContext('2d');
                    Chart.defaults.font.family = "'Inter', sans-serif";
                    
                    if (radarChartInstance) radarChartInstance.destroy();

                    radarChartInstance = new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: ['GDP', 'Inflasi Terkontrol', 'Manajemen Risiko', 'Suhu Terjaga', 'Kekuatan Tukar', 'Nilai Pasok (Efisien)'],
                            datasets: [
                                {
                                    label: c1Data.name,
                                    data: c1Normalized,
                                    backgroundColor: 'rgba(236, 72, 153, 0.2)',
                                    borderColor: 'rgba(236, 72, 153, 1)',
                                    pointBackgroundColor: 'rgba(236, 72, 153, 1)',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: 'rgba(236, 72, 153, 1)',
                                    borderWidth: 2
                                },
                                {
                                    label: c2Data.name,
                                    data: c2Normalized,
                                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    angleLines: { color: 'rgba(0,0,0,0.05)' },
                                    grid: { color: 'rgba(0,0,0,0.05)' },
                                    pointLabels: { font: { size: 12, weight: 600, family: 'Inter' }, color: '#475569' },
                                    ticks: { display: false, min: 0, max: 100 }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { font: { size: 14, weight: 600, family: 'Inter' }, padding: 20, usePointStyle: true }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15,23,42,0.9)',
                                    titleFont: { size: 13, family: 'Inter' },
                                    bodyFont: { size: 13, family: 'Inter' },
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) { label += ': '; }
                                            
                                            const datasetIndex = context.datasetIndex;
                                            const index = context.dataIndex;
                                            const originalData = datasetIndex === 0 ? c1Data.raw_data : c2Data.raw_data;
                                            
                                            // raw_data maps to: gdp, inflation, risk, weather, exchange, efficiency
                                            const units = [' T USD', '%', ' Indeks Risiko', '°C (Indeks Suhu)', ' (vs USD)', ' (Efisensi Pasok)'];
                                            label += originalData[index] + units[index];
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });

                })
                .catch(error => console.error("Error fetching comparison:", error));
        }

        // Handle Dropdown Changes
        document.getElementById('country1').addEventListener('change', function() {
            loadComparison(this.value, document.getElementById('country2').value);
        });

        document.getElementById('country2').addEventListener('change', function() {
            loadComparison(document.getElementById('country1').value, this.value);
        });

        // Initial load
        loadComparison(document.getElementById('country1').value, document.getElementById('country2').value);
    });
</script>
@endsection