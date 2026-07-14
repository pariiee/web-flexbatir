<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tracking — {{ $userName }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; color: #f1f5f9; font-family: system-ui, sans-serif; height: 100vh; display: flex; flex-direction: column; }
        #map { flex: 1; }
        #panel {
            background: #1a1a1a;
            border-top: 1px solid #2a2a2a;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-live { background: #16a34a20; color: #22c55e; border: 1px solid #16a34a40; }
        .badge-offline { background: #ef444420; color: #f87171; border: 1px solid #ef444440; }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
        .dot-live { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        #username { font-weight: 700; font-size: 15px; }
        #last-update { font-size: 11px; color: #888; margin-left: auto; }
        #battery { font-size: 12px; color: #888; }
        #speed-info { font-size: 12px; color: #3b82f6; font-weight: 600; }

        /* Leaflet dark tiles */
        .leaflet-tile { filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7); }
        .leaflet-container { background: #0a0a0a; }
    </style>
</head>
<body>

<div id="map"></div>

<div id="panel">
    <div>
        <span id="status-badge" class="badge badge-{{ $beacon->is_active ? 'live' : 'offline' }}">
            <span class="dot {{ $beacon->is_active ? 'dot-live' : '' }}"></span>
            {{ $beacon->is_active ? 'LIVE' : 'OFFLINE' }}
        </span>
    </div>
    <div>
        <div id="username">{{ $userName }}</div>
        <div id="last-update">Menunggu data...</div>
    </div>
    <div id="battery">🔋 --</div>
    <div id="speed-info">-- km/h</div>
</div>

<script>
    // ── Init Map ──────────────────────────────────────────────────────────────
    const initLat = {{ $beacon->last_lat ?? -6.2088 }};
    const initLng = {{ $beacon->last_lng ?? 106.8456 }};
    const isActive = {{ $beacon->is_active ? 'true' : 'false' }};

    const map = L.map('map', { zoomControl: true }).setView([initLat, initLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    // Custom marker
    const markerIcon = L.divIcon({
        html: `<div style="
            width:20px;height:20px;border-radius:50%;
            background:#3b82f6;border:3px solid white;
            box-shadow:0 0 0 4px rgba(59,130,246,0.3);
        "></div>`,
        className: '',
        iconSize: [20, 20],
        iconAnchor: [10, 10],
    });

    let marker = L.marker([initLat, initLng], { icon: markerIcon }).addTo(map);
    let trail = L.polyline([[initLat, initLng]], {
        color: '#3b82f6',
        weight: 3,
        opacity: 0.7,
    }).addTo(map);

    // ── Pusher / Real-time ────────────────────────────────────────────────────
    if (isActive) {
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster', 'ap1') }}',
            // Untuk Laravel Reverb, ganti dengan:
            // wsHost: '{{ config('reverb.servers.reverb.host', window.location.hostname) }}',
            // wsPort: {{ config('reverb.servers.reverb.port', 8080) }},
            // forceTLS: false,
            // enabledTransports: ['ws'],
        });

        const channel = pusher.subscribe('beacon.{{ $token }}');

        channel.bind('location.updated', function(data) {
            const latlng = [data.lat, data.lng];

            // Update marker
            marker.setLatLng(latlng);
            map.panTo(latlng, { animate: true, duration: 0.5 });

            // Update trail
            trail.addLatLng(latlng);

            // Update panel
            const now = new Date();
            document.getElementById('last-update').textContent =
                'Update: ' + now.toLocaleTimeString('id-ID');

            if (data.battery_level !== null && data.battery_level !== undefined) {
                const bat = data.battery_level;
                const icon = bat > 50 ? '🔋' : bat > 20 ? '🪫' : '🔴';
                document.getElementById('battery').textContent = `${icon} ${bat}%`;
            }

            if (data.speed !== null && data.speed !== undefined) {
                const kmh = (data.speed * 3.6).toFixed(1);
                document.getElementById('speed-info').textContent = `${kmh} km/h`;
            }
        });

        channel.bind('pusher:subscription_error', function() {
            document.getElementById('status-badge').innerHTML =
                '<span class="dot"></span> Gagal connect';
        });
    }

    // ── Fallback: polling setiap 15 detik jika WebSocket tidak tersedia ───────
    @if($beacon->is_active)
    setInterval(async () => {
        try {
            const res = await fetch('/api/live-beacon/peek/{{ $token }}');
            if (!res.ok) return;
            const data = await res.json();
            if (data.lat && data.lng) {
                const latlng = [data.lat, data.lng];
                marker.setLatLng(latlng);
                trail.addLatLng(latlng);
            }
        } catch(e) {}
    }, 15000);
    @endif
</script>
</body>
</html>
