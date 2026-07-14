@extends('layouts.app')

@section('title', 'Port Locations — Supply Chain Risk Intelligence')
@section('breadcrumb', 'Port Locations')

@section('extra_head')
<style>
    #portMap {
        height: 520px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 class="page-title">Port Geospatial Map</h1>
            <p class="page-subtitle">Interactive world map of spatially distributed logistics nodes</p>
        </div>
        <span id="node-count" class="badge-modern badge-violet" style="font-size: 12px; padding: 6px 16px;">
            <i class="ti ti-map-pin" style="font-size: 14px;"></i> Loading nodes...
        </span>
    </div>

    <div class="card-modern animate-fade-up">
        <div class="card-header-modern">
            <span class="card-title-modern"><i class="ti ti-map-2"></i> International Port Distribution</span>
        </div>
        <div id="portMap"></div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    let mapMarkers = [];
    let portMap = null;

    function fetchAndUpdatePorts() {
        fetch('/api/port-locations')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('node-count').innerHTML = `<i class="ti ti-map-pin" style="font-size: 14px;"></i> Detected: ${data.total_nodes} Ports`;

                    mapMarkers.forEach(marker => portMap.removeLayer(marker));
                    mapMarkers = [];

                    data.results.forEach(port => {
                        if (port.latitude && port.longitude) {
                            const popupContent = `
                                <div style="font-family: Inter, sans-serif; padding: 4px;">
                                    <h6 style="font-weight: 700; color: #7c3aed; margin-bottom: 4px; font-size: 14px;">⚓ ${port.port_name}</h6>
                                    <p style="color: #64748b; font-size: 12px; margin-bottom: 2px;">Country: <b style="color: #1e293b;">${port.country_name}</b></p>
                                    <span style="color: #94a3b8; font-size: 11px; font-family: monospace;">Coord: ${port.latitude}, ${port.longitude}</span>
                                </div>
                            `;

                            const marker = L.marker([port.latitude, port.longitude])
                                .addTo(portMap)
                                .bindPopup(popupContent);
                            mapMarkers.push(marker);
                        }
                    });
                }
            })
            .catch(error => {
                console.error("❌ Geospatial Map Error:", error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        portMap = L.map('portMap').setView([10.0, 20.0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(portMap);

        fetchAndUpdatePorts();
        setInterval(fetchAndUpdatePorts, 5000);
    });
</script>
@endsection