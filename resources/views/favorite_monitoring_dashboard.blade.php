<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Monitoring List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">⭐ FAVORITE MONITORING</a>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0 p-4">
            <h5 class="fw-bold mb-4">Pantauan Item Prioritas</h5>
            <div id="favorites-list" class="row g-3">
                <p class="text-muted">Memuat data favorit...</p>
            </div>
        </div>
    </div>

    <script>
        fetch('/api/favorite-monitoring')
            .then(res => res.json())
            .then(data => {
                let html = "";
                data.data.forEach(item => {
                    html += `
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-white">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary">⚓ ${item.name}</h6>
                                    <p class="mb-1 small">Status: <strong>${item.status}</strong></p>
                                    <p class="mb-1 small">Risiko: <span class="badge bg-warning text-dark">${item.risk_level}</span></p>
                                    <p class="text-muted small mt-2">Update: ${item.last_update}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                document.getElementById('favorites-list').innerHTML = html;
            });
    </script>
</body>
</html>