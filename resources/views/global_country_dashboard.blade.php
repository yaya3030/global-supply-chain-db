@extends('layouts.app')

@section('title', 'Global Countries — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Global Countries')

@section('content')
<div class="dashboard-page">
    <div class="page-header">
        <h1 class="page-title">Global Country Analytics</h1>
        <p class="page-subtitle">Country inventory, port distribution, and logistics infrastructure overview</p>
    </div>

    <!-- Top Stats -->
    <div class="stat-grid stagger-children mb-4">
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon violet"><i class="ti ti-globe"></i></div>
            <p class="stat-card-label">Registered Countries</p>
            <p class="stat-card-value" id="metric-countries">—</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon success"><i class="ti ti-anchor"></i></div>
            <p class="stat-card-label">Maritime Port Hubs</p>
            <p class="stat-card-value" id="metric-ports">—</p>
        </div>
        <div class="stat-card animate-fade-up">
            <div class="stat-card-icon warning"><i class="ti ti-list-search"></i></div>
            <p class="stat-card-label">Showing</p>
            <p class="stat-card-value" id="metric-showing">—</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="card-modern animate-fade-up mb-4">
        <div class="card-header-modern">
            <span class="card-title-modern"><i class="ti ti-chart-bar"></i> Top 20 Countries by Port Count</span>
        </div>
        <div style="position: relative; height: 300px;">
            <canvas id="portsDistributionChart"></canvas>
        </div>
    </div>

    <!-- Search + Table -->
    <div class="card-modern animate-fade-up">
        <div class="card-header-modern" style="flex-wrap:wrap; gap:12px;">
            <span class="card-title-modern"><i class="ti ti-table"></i> Country Inventory (250 Countries)</span>
            <div style="display:flex; gap:10px; align-items:center; margin-left:auto;">
                <input type="text" id="countrySearch" placeholder="🔍 Search country..." oninput="filterTable()"
                    style="padding:6px 14px; border:1px solid var(--gray-200); border-radius:8px; font-size:13px; width:220px; outline:none;">
                <select id="regionFilter" onchange="filterTable()"
                    style="padding:6px 12px; border:1px solid var(--gray-200); border-radius:8px; font-size:13px; outline:none;">
                    <option value="">All Regions</option>
                </select>
            </div>
        </div>
        <div style="overflow-x:auto; max-height:520px; overflow-y:auto;">
            <table class="table-modern" style="width:100%;">
                <thead style="position:sticky; top:0; z-index:2; background:white;">
                    <tr>
                        <th>#</th>
                        <th onclick="sortTable('name')" style="cursor:pointer;">Country <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                        <th style="text-align:center;">ISO2</th>
                        <th style="text-align:center;">ISO3</th>
                        <th style="text-align:center;">Currency</th>
                        <th style="text-align:center;">Region</th>
                        <th onclick="sortTable('ports')" style="text-align:center; cursor:pointer;">Ports <i class="ti ti-arrows-sort" style="font-size:11px;"></i></th>
                    </tr>
                </thead>
                <tbody id="country-table-body">
                    <tr><td colspan="7" style="text-align:center; padding:32px; color:var(--gray-400);">Loading data...</td></tr>
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px; border-top:1px solid var(--gray-100); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <span id="paginationInfo" style="font-size:13px; color:var(--gray-500);"></span>
            <div style="display:flex; gap:6px;">
                <button id="btnPrev" onclick="changePage(-1)" style="padding:5px 14px; border:1px solid var(--gray-200); border-radius:6px; background:white; cursor:pointer; font-size:13px;">&#8249; Prev</button>
                <span id="pageIndicator" style="padding:5px 14px; font-size:13px; font-weight:600;"></span>
                <button id="btnNext" onclick="changePage(1)" style="padding:5px 14px; border:1px solid var(--gray-200); border-radius:6px; background:white; cursor:pointer; font-size:13px;">Next &#8250;</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
var allCountries = [];
var filtered = [];
var currentPage = 1;
var perPage = 25;
var sortField = 'name';
var sortAsc = true;

document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/countries-summary')
        .then(function(r) { return r.json(); })
        .then(function(result) {
            if (result.status === 'success') {
                document.getElementById('metric-countries').innerText = result.summary.total_countries + ' Countries';
                document.getElementById('metric-ports').innerText = result.summary.total_monitored_ports + ' Hubs';

                allCountries = result.data;
                filtered = allCountries.slice();

                // Populate region filter
                var regions = [];
                allCountries.forEach(function(c) {
                    if (c.region && regions.indexOf(c.region) === -1) regions.push(c.region);
                });
                regions.sort();
                var sel = document.getElementById('regionFilter');
                regions.forEach(function(r) {
                    var opt = document.createElement('option');
                    opt.value = r;
                    opt.textContent = r;
                    sel.appendChild(opt);
                });

                renderTable();

                // Chart: top 20 by ports
                var top20 = allCountries.slice().sort(function(a,b){return b.ports_count - a.ports_count;}).slice(0, 20);
                var ctx = document.getElementById('portsDistributionChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: top20.map(function(c){ return c.name; }),
                        datasets: [{
                            label: 'Ports',
                            data: top20.map(function(c){ return c.ports_count; }),
                            backgroundColor: 'rgba(124,58,237,0.6)',
                            borderColor: 'rgba(124,58,237,1)',
                            borderWidth: 1.5,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                            x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                        }
                    }
                });
            }
        })
        .catch(function(e) {
            console.error(e);
            document.getElementById('country-table-body').innerHTML = '<tr><td colspan="7" style="text-align:center;color:red;padding:24px;">Failed to load country data.</td></tr>';
        });
});

function filterTable() {
    var q = document.getElementById('countrySearch').value.toLowerCase();
    var r = document.getElementById('regionFilter').value;
    filtered = allCountries.filter(function(c) {
        var nameMatch = !q || c.name.toLowerCase().indexOf(q) !== -1
            || (c.iso2 && c.iso2.toLowerCase().indexOf(q) !== -1)
            || (c.iso3 && c.iso3.toLowerCase().indexOf(q) !== -1)
            || (c.currency_code && c.currency_code.toLowerCase().indexOf(q) !== -1);
        var regionMatch = !r || c.region === r;
        return nameMatch && regionMatch;
    });
    currentPage = 1;
    renderTable();
}

function sortTable(field) {
    if (sortField === field) { sortAsc = !sortAsc; } else { sortField = field; sortAsc = true; }
    filtered.sort(function(a, b) {
        var va = field === 'ports' ? (a.ports_count || 0) : (a.name || '');
        var vb = field === 'ports' ? (b.ports_count || 0) : (b.name || '');
        if (va < vb) return sortAsc ? -1 : 1;
        if (va > vb) return sortAsc ? 1 : -1;
        return 0;
    });
    renderTable();
}

function changePage(dir) {
    var total = Math.ceil(filtered.length / perPage);
    currentPage = Math.max(1, Math.min(total, currentPage + dir));
    renderTable();
}

function renderTable() {
    var total = filtered.length;
    var totalPages = Math.max(1, Math.ceil(total / perPage));
    var start = (currentPage - 1) * perPage;
    var end = Math.min(start + perPage, total);
    var rows = filtered.slice(start, end);

    document.getElementById('metric-showing').innerText = total + ' of ' + allCountries.length;
    document.getElementById('paginationInfo').innerText = 'Showing ' + (start + 1) + ' - ' + end + ' of ' + total + ' countries';
    document.getElementById('pageIndicator').innerText = 'Page ' + currentPage + ' / ' + totalPages;
    document.getElementById('btnPrev').disabled = currentPage <= 1;
    document.getElementById('btnNext').disabled = currentPage >= totalPages;

    var html = '';
    rows.forEach(function(c, i) {
        var portsBadge = c.ports_count > 0
            ? '<span style="background:#ede9fe;color:#7c3aed;padding:2px 10px;border-radius:10px;font-weight:700;">' + c.ports_count + '</span>'
            : '<span style="color:#94a3b8;">0</span>';
        html += '<tr>'
            + '<td style="color:var(--gray-400);font-size:12px;">' + (start + i + 1) + '</td>'
            + '<td style="font-weight:600;color:var(--gray-800);">' + (c.name || '-') + '</td>'
            + '<td style="text-align:center;color:var(--gray-500);font-size:13px;">' + (c.iso2 || '-') + '</td>'
            + '<td style="text-align:center;color:var(--gray-500);font-size:13px;">' + (c.iso3 || '-') + '</td>'
            + '<td style="text-align:center;"><span class="badge-modern badge-violet" style="font-family:monospace;">' + (c.currency_code || '-') + '</span></td>'
            + '<td style="text-align:center;font-size:12px;color:var(--gray-500);">' + (c.region || '-') + '</td>'
            + '<td style="text-align:center;">' + portsBadge + '</td>'
            + '</tr>';
    });

    if (html === '') {
        html = '<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--gray-400);">No countries found matching your search.</td></tr>';
    }

    document.getElementById('country-table-body').innerHTML = html;
}
</script>
@endsection