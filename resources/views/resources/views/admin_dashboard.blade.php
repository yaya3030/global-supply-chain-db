<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin System Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-dark text-white">

    <div class="container py-5">
        <h2 class="mb-4 text-warning fw-bold">Admin Control Center</h2>
        
        <div class="row g-4" id="stats-container">
            <!-- Cards statis akan diisi oleh AJAX -->
            <div class="col-md-3"><div class="card p-3 bg-secondary border-0">Loading...</div></div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card p-4 bg-light text-dark">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch('/api/admin-stats')
            .then(res => res.json())
            .then(res => {
                const data = res.data;
                // Render Cards (Ringkas)
                document.getElementById('stats-container').innerHTML = `
                    <div class="col-md-3"><div class="card p-3 bg-primary text-white border-0">Users: ${data.summary.active_users}</div></div>
                    <div class="col-md-3"><div class="card p-3 bg-success text-white border-0">Health: ${data.summary.system_health}</div></div>
                    <div class="col-md-3"><div class="card p-3 bg-info text-white border-0">Latency: ${data.summary.api_load}</div></div>
                    <div class="col-md-3"><div class="card p-3 bg-warning text-dark border-0">Modules: ${data.summary.active_modules}</div></div>
                `;

                // Render Chart
                new Chart(document.getElementById('trafficChart'), {
                    type: 'line',
                    data: {
                        labels: ['1h', '2h', '3h', '4h', '5h', '6h', '7h'],
                        datasets: [{ label: 'System Traffic', data: data.traffic_data, borderColor: '#ffc107' }]
                    }
                });
            });
    </script>
</body>
</html>