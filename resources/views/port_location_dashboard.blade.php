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
    .animated-route {
        stroke-dasharray: 10, 10;
        animation: dash 20s linear infinite;
    }
    @keyframes dash {
        to { stroke-dashoffset: -1000; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .leaflet-popup-content-wrapper {
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">Port Geospatial Map</h1>
            <p class="page-subtitle">Interactive world map of spatially distributed logistics nodes</p>
        </div>
        <div style="display:flex; align-items:center; gap:16px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px; font-weight:600; color:var(--gray-700);">
                <input type="checkbox" id="toggleRoutes" style="width:16px; height:16px; cursor:pointer;">
                Show Shipping Routes
            </label>
            <span id="node-count" class="badge-modern badge-violet" style="font-size:12px; padding:6px 16px;">
                <i class="ti ti-map-pin" style="font-size:14px;"></i> Loading nodes...
            </span>
        </div>
    </div>

    <div class="card-modern animate-fade-up">
        <div class="card-header-modern">
            <span class="card-title-modern"><i class="ti ti-map-2"></i> International Port Distribution</span>
        </div>
        <div style="position:relative;">
            <div id="portMap"></div>
            <div style="position:absolute; bottom:24px; right:24px; z-index:1000; background:#4a4542; padding:10px 16px; border-radius:8px; display:flex; gap:16px; color:#f8fafc; font-size:13px; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,0.15); align-items:center; pointer-events:none;">
                <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:50%; background-color:#8bb279;"></span> Clear / Calm</div>
                <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:50%; background-color:#df9f52;"></span> Rain / Strong Winds</div>
                <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:50%; background-color:#c95f46;"></span> Storm Warning</div>
            </div>
        </div>
    </div>
</div>

<!-- Goods Tracking Modal -->
<div id="goodsModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:white; border-radius:12px; width:620px; max-width:95%; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 10px 40px rgba(0,0,0,0.25);">
        <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(135deg,#7c3aed 0%,#5b21b6 100%); border-radius:12px 12px 0 0;">
            <h3 id="goodsModalTitle" style="margin:0; font-size:18px; font-weight:700; color:white;">Port Goods Tracking</h3>
            <button onclick="document.getElementById('goodsModal').style.display='none'" style="background:rgba(255,255,255,0.2); border:none; width:30px; height:30px; border-radius:50%; font-size:16px; cursor:pointer; color:white; display:flex; align-items:center; justify-content:center;">&times;</button>
        </div>
        <div id="goodsModalContent" style="padding:20px; overflow-y:auto; flex:1;">
            <div style="text-align:center; color:#64748b; padding:40px;">Loading...</div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
var mapMarkers = {};
var routeLines = [];
var portMap = null;

document.getElementById('toggleRoutes').addEventListener('change', function(e) {
    if (e.target.checked) { fetchAndDrawRoutes(); } else { clearRoutes(); }
});

function clearRoutes() {
    routeLines.forEach(function(l) { portMap.removeLayer(l); });
    routeLines = [];
}

function fetchAndDrawRoutes() {
    fetch('/api/shipping-routes').then(function(r){return r.json();}).then(function(data){
        if (data.status==='success') {
            clearRoutes();
            data.results.forEach(function(route) {
                var latlngs = route.waypoints || [[route.origin.lat,route.origin.lng],[route.destination.lat,route.destination.lng]];
                var color = route.status==='smooth' ? '#10b981' : (route.status==='weather_delay' ? '#0ea5e9' : route.status==='economic_delay' ? '#ef4444' : '#8b5cf6');
                var pl = L.polyline(latlngs,{color:color,weight:4,opacity:0.9,className:'animated-route'}).addTo(portMap);
                routeLines.push(pl);
            });
        }
    }).catch(function(e){console.error(e);});
}

function fetchAndUpdatePorts() {
    fetch('/api/port-locations').then(function(r){return r.json();}).then(function(data){
        if (data.status==='success') {
            document.getElementById('node-count').innerHTML = '<i class="ti ti-map-pin"></i> Detected: '+data.total_nodes+' Ports';
            var ids = [];
            data.results.forEach(function(port) {
                if (!port.latitude || !port.longitude) return;
                var mid = port.id ? String(port.id) : 'hub_'+port.port_name;
                ids.push(mid);
                if (!mapMarkers[mid]) {
                    var btn = port.id ? '<button onclick="showPortGoods('+port.id+')" style="background:#7c3aed;color:white;border:none;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;width:100%;margin-top:8px;">Tracking Goods</button>' : '';
                    var popup = '<div style="font-family:Inter,sans-serif;padding:4px;min-width:200px;">'+
                        '<h6 style="font-weight:700;color:#7c3aed;margin-bottom:4px;font-size:14px;">' + port.port_name + '</h6>'+
                        '<p style="color:#64748b;font-size:12px;margin-bottom:4px;">Country: <b>' + port.country_name + '</b></p>'+
                        btn+'</div>';
                    mapMarkers[mid] = L.marker([port.latitude,port.longitude]).addTo(portMap).bindPopup(popup);
                }
            });
            Object.keys(mapMarkers).forEach(function(id) {
                if (ids.indexOf(id)===-1) { portMap.removeLayer(mapMarkers[id]); delete mapMarkers[id]; }
            });
        }
    }).catch(function(e){console.error('Port load error:',e);});
}

function showPortGoods(portId) {
    var modal = document.getElementById('goodsModal');
    modal.style.display = 'flex';
    document.getElementById('goodsModalTitle').innerText = 'Loading...';
    document.getElementById('goodsModalContent').innerHTML = '<div style="text-align:center;color:#64748b;padding:40px;">Loading goods data...</div>';

    fetch('/api/port-goods/'+portId).then(function(r){return r.json();}).then(function(data){
        if (data.status==='success') {
            document.getElementById('goodsModalTitle').innerText = 'Goods at '+data.port_name;
            if (!data.goods || data.goods.length===0) {
                document.getElementById('goodsModalContent').innerHTML = '<div style="text-align:center;color:#64748b;padding:40px;">No goods at this port right now.</div>';
                return;
            }
            var html = '<div style="display:flex;flex-direction:column;gap:12px;">';
            data.goods.forEach(function(good) {
                var isD = good.current_status==='delayed';
                var badge = isD
                    ? '<span style="background:#fee2e2;color:#ef4444;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:700;">STUCK/DELAYED</span>'
                    : '<span style="background:#dcfce7;color:#10b981;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:700;">ARRIVED</span>';
                var hist = '';
                if (good.route_history && good.route_history.length) {
                    good.route_history.forEach(function(r) {
                        var dot = r.status==='delayed' ? '🔴' : (r.status==='arrived' ? '🟢' : '⚪');
                        var arr = r.arrival_time ? new Date(r.arrival_time).toLocaleString() : '-';
                        var dep = r.departure_time ? new Date(r.departure_time).toLocaleString() : '(Current)';
                        hist += '<div style="display:flex;gap:10px;padding:5px 0;border-bottom:1px solid #f1f5f9;font-size:12px;">'+
                            '<span>'+dot+'</span>'+
                            '<div><div style="font-weight:600;color:#334155;">'+r.port_name+'</div>'+
                            '<div style="color:#94a3b8;font-size:11px;">'+arr+' → '+dep+'</div></div></div>';
                    });
                } else { hist = '<div style="color:#94a3b8;font-size:12px;">No history yet.</div>'; }

                html += '<div style="border:1px solid '+(isD?'#fca5a5':'#bbf7d0')+
                    ';border-radius:10px;padding:14px;background:'+(isD?'#fff5f5':'#f0fdf4')+';">'+
                    '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px;">'+
                    '<div style="font-weight:700;color:#0f172a;font-size:14px;">'+good.name+'</div>'+badge+'</div>'+
                    '<div style="font-size:11px;color:#64748b;font-family:monospace;margin-bottom:10px;background:#f8fafc;padding:3px 8px;border-radius:4px;display:inline-block;">TRK: '+good.tracking_number+'</div>'+
                    '<div style="font-size:12px;font-weight:700;color:#475569;margin-bottom:4px;">Route History:</div>'+
                    '<div style="padding-left:4px;">'+hist+'</div></div>';
            });
            html += '</div>';
            document.getElementById('goodsModalContent').innerHTML = html;
        } else {
            document.getElementById('goodsModalContent').innerHTML = '<div style="color:#ef4444;padding:20px;text-align:center;">Error loading data.</div>';
        }
    }).catch(function(err) {
        document.getElementById('goodsModalContent').innerHTML = '<div style="color:#ef4444;padding:20px;text-align:center;">Failed to fetch data.</div>';
        console.error(err);
    });
}

document.getElementById('goodsModal').addEventListener('click', function(e) {
    if (e.target===this) this.style.display='none';
});

document.addEventListener('DOMContentLoaded', function() {
    portMap = L.map('portMap').setView([10.0, 20.0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(portMap);
    setTimeout(function() {
        portMap.invalidateSize();
        fetchAndUpdatePorts();
    }, 400);
    setInterval(fetchAndUpdatePorts, 5000);
});
</script>
@endsection
