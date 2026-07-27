@extends('layouts.app')

@section('title', 'Import Analyzer — Decision Support')
@section('breadcrumb', 'Import Analyzer')

@section('extra_head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .analyzer-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }

    @media (min-width: 992px) {
        .analyzer-container {
            grid-template-columns: 400px 1fr;
        }
    }

    .form-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-md);
    }

    .form-panel-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--gray-100);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .form-select {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1.5px solid var(--gray-300);
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--gray-800);
        background: #f8fafc;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-select:focus {
        border-color: var(--violet-500);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
    }

    .btn-analyze {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, var(--violet-600) 0%, var(--violet-700) 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3);
    }

    .btn-analyze:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
    }

    /* Result Panel */
    .result-panel {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-md);
        display: none;
        flex-direction: column;
    }

    .result-header {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--gray-100);
    }

    .total-score-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        color: white;
        box-shadow: var(--shadow-lg);
    }

    .score-high { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .score-medium { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .score-low { background: linear-gradient(135deg, #10b981, #059669); }

    .ts-num { font-size: 2.5rem; font-weight: 900; line-height: 1; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .ts-lbl { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-top: 4px; opacity: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.1); }

    .recommendation-alert {
        padding: 16px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .rec-high { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .rec-medium { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .rec-low { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

    /* 5 Pillars Grid */
    .pillars-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .pillar-card {
        background: #f8fafc;
        border: 1px solid var(--gray-200);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        gap: 16px;
        align-items: center;
        transition: transform 0.2s ease;
    }

    .pillar-card:hover {
        transform: translateX(4px);
        background: #ffffff;
        box-shadow: var(--shadow-sm);
    }

    .pillar-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .p-weather { background: #e0f2fe; color: #0284c7; }
    .p-currency { background: #dcfce7; color: #15803d; }
    .p-geo { background: #fce7f3; color: #db2777; }
    .p-congestion { background: #fef3c7; color: #d97706; }
    .p-inflation { background: #f3e8ff; color: #7c3aed; }

    .pillar-content {
        flex: 1;
    }

    .pillar-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--gray-900);
        margin-bottom: 4px;
        display: flex;
        justify-content: space-between;
    }
    
    .pillar-score {
        color: var(--violet-600);
        font-family: monospace;
        background: var(--violet-50);
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.85rem;
    }

    .pillar-desc {
        font-size: 0.9rem;
        color: var(--gray-600);
        line-height: 1.4;
    }
    
    /* Loading overlay */
    #loadingOverlay {
        display: none;
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(4px);
        z-index: 10;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 16px;
        border-radius: 16px;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="page-header" style="margin-bottom: 24px;">
        <h1 class="page-title" style="display:flex; align-items:center; gap:10px;">
            <i class="ti ti-scale" style="color: var(--violet-600);"></i> Import Analyzer (Decision Support)
        </h1>
        <p class="page-subtitle">Simulasi pengambilan keputusan impor berdasarkan 5 pilar risiko utama logistik.</p>
    </div>

    <div class="analyzer-container">
        
        <!-- FORM PANEL -->
        <div class="form-panel animate-fade-up">
            <div class="form-panel-title">
                <i class="ti ti-route"></i> Jalur Pengiriman
            </div>
            
            <div class="form-group">
                <label class="form-label">Negara Asal (Eksportir)</label>
                <select id="originCountry" class="form-select" onchange="loadPorts('originCountry', 'originPort')">
                    <option value="" disabled selected>Pilih Negara Asal</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pelabuhan Muat (Origin Port)</label>
                <select id="originPort" class="form-select" disabled>
                    <option value="" disabled selected>Pilih Negara Asal Terlebih Dahulu</option>
                </select>
            </div>

            <div style="text-align: center; margin: -10px 0 10px; color: var(--gray-400);">
                <i class="ti ti-arrow-down" style="font-size: 1.5rem;"></i>
            </div>

            <div class="form-group">
                <label class="form-label">Negara Tujuan (Importir)</label>
                <select id="destCountry" class="form-select" onchange="loadPorts('destCountry', 'destPort')">
                    <option value="" disabled selected>Pilih Negara Tujuan</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pelabuhan Bongkar (Destination Port)</label>
                <select id="destPort" class="form-select" disabled>
                    <option value="" disabled selected>Pilih Negara Tujuan Terlebih Dahulu</option>
                </select>
            </div>

            <button class="btn-analyze" id="btnAnalyze" onclick="analyzeImport()">
                <i class="ti ti-search"></i> Analisis Risiko Impor
            </button>
        </div>

        <!-- RESULT PANEL -->
        <div style="position: relative;">
            <div id="loadingOverlay">
                <div class="spinner" style="width:40px; height:40px; border:4px solid var(--violet-200); border-top:4px solid var(--violet-600); border-radius:50%; animation:spin 1s linear infinite;"></div>
                <div style="font-weight:700; color:var(--violet-700); text-shadow: 0 1px 2px rgba(255,255,255,0.8);">Memproses Simulasi Logistik...</div>
                <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
            </div>

            <div class="result-panel animate-fade-up" id="resultPanel" style="animation-delay: 100ms;">
                <div class="result-header">
                    <div id="scoreCircle" class="total-score-circle score-low">
                        <span class="ts-num" id="r-total">0</span>
                        <span class="ts-lbl">Total Risk</span>
                    </div>
                    <div id="recommendationAlert" class="recommendation-alert rec-low">
                        —
                    </div>
                </div>

                <div class="pillars-grid">
                    <!-- 1. Cuaca -->
                    <div class="pillar-card">
                        <div class="pillar-icon p-weather"><i class="ti ti-cloud-storm"></i></div>
                        <div class="pillar-content">
                            <div class="pillar-title">Cuaca & Iklim <span class="pillar-score" id="s-weather">0/100</span></div>
                            <div class="pillar-desc" id="d-weather">Cuaca buruk dapat mengganggu pengiriman.</div>
                        </div>
                    </div>
                    
                    <!-- 2. Nilai Tukar -->
                    <div class="pillar-card">
                        <div class="pillar-icon p-currency"><i class="ti ti-currency-dollar"></i></div>
                        <div class="pillar-content">
                            <div class="pillar-title">Nilai Tukar Mata Uang <span class="pillar-score" id="s-currency">0/100</span></div>
                            <div class="pillar-desc" id="d-currency">Nilai tukar mata uang berubah.</div>
                        </div>
                    </div>

                    <!-- 3. Geopolitik -->
                    <div class="pillar-card">
                        <div class="pillar-icon p-geo"><i class="ti ti-shield-exclamation"></i></div>
                        <div class="pillar-content">
                            <div class="pillar-title">Geopolitik & Keamanan <span class="pillar-score" id="s-geo">0/100</span></div>
                            <div class="pillar-desc" id="d-geo">Konflik geopolitik meningkatkan risiko.</div>
                        </div>
                    </div>

                    <!-- 4. Kemacetan -->
                    <div class="pillar-card">
                        <div class="pillar-icon p-congestion"><i class="ti ti-anchor"></i></div>
                        <div class="pillar-content">
                            <div class="pillar-title">Kemacetan Pelabuhan <span class="pillar-score" id="s-congestion">0/100</span></div>
                            <div class="pillar-desc" id="d-congestion">Kemacetan pelabuhan menyebabkan keterlambatan.</div>
                        </div>
                    </div>

                    <!-- 5. Inflasi -->
                    <div class="pillar-card">
                        <div class="pillar-icon p-inflation"><i class="ti ti-trending-up"></i></div>
                        <div class="pillar-content">
                            <div class="pillar-title">Inflasi Biaya <span class="pillar-score" id="s-inflation">0/100</span></div>
                            <div class="pillar-desc" id="d-inflation">Inflasi suatu negara mempengaruhi biaya produksi.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    function loadPorts(countrySelectId, portSelectId) {
        const countryId = document.getElementById(countrySelectId).value;
        const portSelect = document.getElementById(portSelectId);
        
        if (!countryId) {
            portSelect.innerHTML = '<option value="" disabled selected>Pilih Negara Terlebih Dahulu</option>';
            portSelect.disabled = true;
            return;
        }

        portSelect.innerHTML = '<option value="" disabled selected>Memuat pelabuhan...</option>';
        portSelect.disabled = true;

        fetch(`/api/ports-by-country/${countryId}`)
            .then(res => res.json())
            .then(res => {
                if(res.status === 'success') {
                    if (res.data.length === 0) {
                        portSelect.innerHTML = '<option value="" disabled selected>Tidak ada data pelabuhan</option>';
                    } else {
                        portSelect.innerHTML = '<option value="" disabled selected>Pilih Pelabuhan</option>' + 
                            res.data.map(p => `<option value="${p.id}">${p.port_name}</option>`).join('');
                        portSelect.disabled = false;
                    }
                }
            })
            .catch(err => console.error(err));
    }

    function analyzeImport() {
        const oC = document.getElementById('originCountry').value;
        const oP = document.getElementById('originPort').value;
        const dC = document.getElementById('destCountry').value;
        const dP = document.getElementById('destPort').value;

        if (!oC || !oP || !dC || !dP) {
            alert('Mohon lengkapi seluruh rute pengiriman (Negara & Pelabuhan) terlebih dahulu.');
            return;
        }

        const btn = document.getElementById('btnAnalyze');
        const overlay = document.getElementById('loadingOverlay');
        const resultPanel = document.getElementById('resultPanel');

        btn.disabled = true;
        overlay.style.display = 'flex';
        resultPanel.style.display = 'none';

        fetch('/api/analyze-import', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                origin_country_id: oC,
                origin_port_id: oP,
                dest_country_id: dC,
                dest_port_id: dP
            })
        })
        .then(res => res.json())
        .then(res => {
            if(res.status === 'success') {
                const data = res.data;
                const p = data.pillars;

                // Update Total Score
                document.getElementById('r-total').innerText = data.total_score;
                const circle = document.getElementById('scoreCircle');
                const recAlert = document.getElementById('recommendationAlert');
                
                circle.className = 'total-score-circle';
                recAlert.className = 'recommendation-alert';
                
                if (data.total_score >= 60) {
                    circle.classList.add('score-high');
                    recAlert.classList.add('rec-high');
                } else if (data.total_score >= 40) {
                    circle.classList.add('score-medium');
                    recAlert.classList.add('rec-medium');
                } else {
                    circle.classList.add('score-low');
                    recAlert.classList.add('rec-low');
                }
                
                recAlert.innerText = "Rekomendasi: " + data.recommendation;

                // Update Pillars
                document.getElementById('s-weather').innerText = p.weather.score + '/100';
                document.getElementById('d-weather').innerText = p.weather.desc;

                document.getElementById('s-currency').innerText = p.currency.score + '/100';
                document.getElementById('d-currency').innerText = p.currency.desc;

                document.getElementById('s-geo').innerText = p.geopolitics.score + '/100';
                document.getElementById('d-geo').innerText = p.geopolitics.desc;

                document.getElementById('s-congestion').innerText = p.congestion.score + '/100';
                document.getElementById('d-congestion').innerText = p.congestion.desc;

                document.getElementById('s-inflation').innerText = p.inflation.score + '/100';
                document.getElementById('d-inflation').innerText = p.inflation.desc;

                setTimeout(() => {
                    overlay.style.display = 'none';
                    resultPanel.style.display = 'flex';
                    btn.disabled = false;
                }, 800); // Fake delay for UX simulation feel
            }
        })
        .catch(err => {
            console.error(err);
            overlay.style.display = 'none';
            btn.disabled = false;
            alert("Terjadi kesalahan saat memproses data.");
        });
    }
</script>
@endsection
