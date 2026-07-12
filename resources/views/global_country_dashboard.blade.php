@extends('layouts.app')

@section('content')
<style>
    .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
</style>

<!-- Header Halaman -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark">Global Country Analytics</h3>
    <span class="text-muted">Stage 3: Supply Chain Engine</span>
</div>

<div class="container-fluid p-0">
    <!-- Top Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card p-3 bg-white border-start border-info border-4">
                <div class="text-muted small text-uppercase fw-bold">Total Terdaftar</div>
                <div id="metric-countries" class="h3 fw-bold text-dark">- Negara</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 bg-white border-start border-success border-4">
                <div class="text-muted small text-uppercase fw-bold">Total Infrastruktur Pelabuhan</div>
                <div id="metric-ports" class="h3 fw-bold text-dark">- Titik Port</div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="row g-4">
        <!-- Left Column: Chart -->
        <div class="col-lg-6">
            <div class="card p-4 bg-white h-100">
                <h5 class="card-title fw-bold text-secondary mb-3">Distribusi Pelabuhan per Negara</h5>
                <div class="chart-container flex-grow-1 d-flex align-items-center justify-content-center">
                    <canvas id="portsDistributionChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column: Table -->
        <div class="col-lg-6">
            <div class="card p-4 bg-white h-100">
                <h5 class="card-title fw-bold text-secondary mb-3">Data Inventori Negara</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Negara</th>
                                <th class="text-center">ISO2</th>
                                <th class="text-center">ISO3</th>
                                <th class="text-center">Mata Uang</th>
                                <th class="text-center">Aset Port</th>
                            </tr>
                        </thead>
                        <tbody id="country-table-body">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Memproses data via AJAX...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // AJAX Fetching
        fetch('/api/countries-summary')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    // Update Metrics
                    document.getElementById('metric-countries').innerText = `${result.summary.total_countries} Negara`;
                    document.getElementById('metric-ports').innerText = `${result.summary.total_monitored_ports} Hub Maritim`;

                    // Prepare Chart Data
                    const labelNegara = [];
                    const dataJumlahPort = [];
                    let tableRowsHtml = "";

                    result.data.forEach(country => {
                        labelNegara.push(country.name);
                        dataJumlahPort.push(country.ports_count);

                        tableRowsHtml += `
                            <tr>
                                <td class="fw-bold text-dark">${country.name}</td>
                                <td class="text-center text-secondary small">${country.iso2 ?? '-'}</td>
                                <td class="text-center text-secondary small">${country.iso3 ?? '-'}</td>
                                <td class="text-center"><span class="badge bg-secondary font-monospace">${country.currency_code ?? '-'}</span></td>
                                <td class="text-center fw-bold text-info">${country.ports_count}</td>
                            </tr>
                        `;
                    });

                    // Render Table
                    document.getElementById('country-table-body').innerHTML = tableRowsHtml;

                    // Render Chart
                    const ctx = document.getElementById('portsDistributionChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labelNegara,
                            datasets: [{
                                label: 'Jumlah Pelabuhan',
                                data: dataJumlahPort,
                                backgroundColor: 'rgba(13, 202, 240, 0.6)',
                                borderColor: 'rgba(13, 202, 240, 1)',
                                borderWidth: 1.5,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                        }
                    });
                }
            })
            .catch(error => {
                console.error("Kesalahan AJAX:", error);
                document.getElementById('country-table-body').innerHTML = `
                    <tr><td colspan="5" class="text-center text-danger py-4">Gagal memproses data.</td></tr>
                `;
            });
    });
</script>
@endpush