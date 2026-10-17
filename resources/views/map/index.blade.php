@extends('layouts.app')

@section('title', 'Haunt Map — Phantom Trade')

@section('content')

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />

    {{-- PAGE HEADER --}}
    <div class="flex justify-between items-end gap-4 mb-5 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Haunt Map
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Locate hunters, merchants, and active wanted ads.
            </p>
        </div>

    </div>


    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <section class="mb-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold text-[var(--text)]">Deploy equipment on the Haunt Map</h2>
                <p class="text-sm text-[var(--text-secondary)]">Select equipment you own, then click the existing map to choose where to deploy it. Deployment does not grant hunting access.</p>
            </div>
            <a href="/gears" class="btn">Browse Gear Shop</a>
        </div>
        @if ($owned->isEmpty())
            <p class="rounded-lg border border-dashed border-[var(--border)] p-4 text-sm text-[var(--text-secondary)]">You have no deployable equipment in your inventory yet. Buy a ward, anchor, trap, or scanner from the Gear Shop first.</p>
        @else
            <form method="POST" action="/protection/deploy" id="deployForm" class="grid grid-cols-1 gap-3 md:grid-cols-4">
                @csrf
                <div>
                    <label for="gear_id" class="mb-1 block text-xs font-semibold text-[var(--text-secondary)]">Equipment</label>
                    <select id="gear_id" name="gear_id" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] p-2.5 text-sm text-[var(--text)]">
                        <option value="">Choose owned equipment...</option>
                        @foreach ($owned as $item)
                            <option value="{{ $item->gear->id }}">{{ $item->gear->name }} ({{ $item->quantity }} owned)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="label" class="mb-1 block text-xs font-semibold text-[var(--text-secondary)]">Label (optional)</label>
                    <input id="label" name="label" maxlength="80" placeholder="e.g. North entrance" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] p-2.5 text-sm text-[var(--text)]">
                </div>
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
                <div id="pickedCoordinates" class="self-end rounded-lg bg-[var(--surface-soft)] p-2.5 text-xs text-[var(--text-secondary)]">Click the map to choose a location.</div>
                <div class="flex items-end">
                    <button type="submit" class="btn w-full">Deploy on map</button>
                </div>
            </form>
        @endif
        <p class="mt-3 text-xs text-[var(--text-secondary)]">Civilians, merchants, and other non-hunter users may deploy owned equipment, but hunting remains restricted to Ghost Hunters by the existing hunt routes.</p>
    </section>

    {{-- FILTER CHIPS --}}
    <div class="flex gap-2 mb-4 flex-wrap items-center">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)] mr-1.5">
            Show
        </span>

        <button type="button" class="map-filter active" data-filter="users">
            <i data-lucide="users" class="w-[13px] h-[13px]"></i>
            <span>Users</span>
        </button>

        <button type="button" class="map-filter active" data-filter="wanted">
            <i data-lucide="tag" class="w-[13px] h-[13px]"></i>
            <span>Wanted Ads</span>
        </button>

        <button type="button" class="map-filter active" data-filter="merchants">
            <i data-lucide="store" class="w-[13px] h-[13px]"></i>
            <span>Merchants</span>
        </button>
    </div>

    {{-- MAP CONTAINER --}}
    <div class="relative rounded-[var(--radius)] overflow-hidden [border:1px_solid_var(--border)] bg-[var(--surface)] [box-shadow:0_4px_16px_rgba(15,_23,_42,_0.06)]">

        <div id="map" class="h-[640px] w-full bg-[#f1f5f9]"></div>

        {{-- Satellite / Standard toggle --}}
        <div class="absolute top-[14px] right-[14px] z-[500] flex gap-0.5 bg-[var(--surface)] pt-1 pr-1 pb-1 pl-1 rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] [box-shadow:0_4px_12px_rgba(15,_23,_42,_0.08)]">
            <button id="btnSatellite" type="button" class="inline-flex items-center gap-1.5 bg-[var(--accent)] text-[#fff] [border:none] pt-[7px] pr-3 pb-[7px] pl-3 rounded font-semibold text-[12px] cursor-pointer [font-family:inherit]"
                   >
                <i data-lucide="satellite" class="w-[13px] h-[13px]"></i>
                <span>Satellite</span>
            </button>
            <button id="btnStandard" type="button" class="inline-flex items-center gap-1.5 bg-[transparent] text-[var(--text-secondary)] [border:none] pt-[7px] pr-3 pb-[7px] pl-3 rounded font-semibold text-[12px] cursor-pointer [font-family:inherit]"
                   >
                <i data-lucide="map" class="w-[13px] h-[13px]"></i>
                <span>Standard</span>
            </button>
        </div>

        {{-- Legend --}}
        <div class="absolute bottom-[14px] left-[14px] z-[500] bg-[var(--surface)] [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] pt-3 pr-3.5 pb-3 pl-3.5 [box-shadow:0_4px_12px_rgba(15,_23,_42,_0.08)] text-[12px]">
            <div class="text-[10.5px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)] mb-2">
                Legend
            </div>
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#7c3aed]"></span>
                    <span class="text-[var(--text-secondary)]">Merchant</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#059669]"></span>
                    <span class="text-[var(--text-secondary)]">Ghost Hunter</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0284c7]"></span>
                    <span class="text-[var(--text-secondary)]">Civilian</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#d97706]"></span>
                    <span class="text-[var(--text-secondary)]">Wanted Ad</span>
                </div>
            </div>
        </div>

        {{-- Empty state --}}
        <div id="emptyState" class="hidden absolute top-[50%] left-[50%] [transform:translate(-50%,_-50%)] bg-[var(--surface)] pt-6 pr-8 pb-6 pl-8 rounded-[var(--radius)] [border:1px_solid_var(--border)] text-center z-[400] max-w-80 [box-shadow:0_8px_32px_rgba(15,_23,_42,_0.12)]">
            <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="map-pin" class="w-5 h-5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                No pins to show
            </div>
            <div class="text-[12.5px] text-[var(--text-muted)]">
                Users who set their location will appear here.
            </div>
        </div>

    </div>

    {{-- MAP STYLES --}}
    <style>
        .map-filter {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            font-size: 12.5px;
            font-weight: 500;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
        }
        .map-filter:hover {
            border-color: var(--text-muted);
            color: var(--text);
        }
        .map-filter.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        .map-filter.active svg { color: #fff; }

        .leaflet-container {
            background: #f1f5f9;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* Fade-in animation — only opacity, never transform */
        @keyframes pinFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .leaflet-marker-icon {
            animation: pinFadeIn 0.35s ease-out both;
        }

        /* Marker hover — apply to inner SVG only (never the wrapper) */
        .custom-marker svg {
            filter: drop-shadow(0 2px 4px rgba(15, 23, 42, 0.15));
            transition: filter 0.15s ease-out;
        }
        .custom-marker:hover svg {
            filter: drop-shadow(0 6px 12px rgba(15, 23, 42, 0.35));
        }

        /* Popup styling */
        .leaflet-popup-content-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
            padding: 0;
        }
        .leaflet-popup-content {
            margin: 0;
            padding: 14px 16px;
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.5;
            min-width: 200px;
        }
        .leaflet-popup-tip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }
        .leaflet-popup-close-button {
            color: #94a3b8 !important;
            font-size: 18px !important;
            padding: 6px 8px 0 0 !important;
        }
        .leaflet-popup-close-button:hover {
            color: #0f172a !important;
        }

        /* Leaflet control styling */
        .leaflet-control-zoom a {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #e2e8f0 !important;
            width: 32px !important;
            height: 32px !important;
            line-height: 30px !important;
            font-size: 16px !important;
        }
        .leaflet-control-zoom a:hover {
            background: #f8fafc !important;
        }
        .leaflet-control-scale-line {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid #e2e8f0 !important;
            border-top: none !important;
            color: #64748b !important;
            font-size: 11px !important;
            padding: 2px 6px !important;
        }
        .leaflet-control-attribution {
            background: rgba(255, 255, 255, 0.9) !important;
            color: #94a3b8 !important;
            font-size: 10px !important;
            padding: 2px 6px !important;
        }
    </style>

    {{-- Leaflet JS --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- MAP SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Create map
            const map = L.map('map', {
                center: [12.8797, 121.7740],
                zoom: 6,
                minZoom: 3,
                maxZoom: 19,
                zoomControl: true,
                scrollWheelZoom: true,
            });

            // 2. Tiles
            const satellite = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                { maxNativeZoom: 17, maxZoom: 19, attribution: 'Tiles &copy; Esri' }
            );

            const standard = L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                { maxNativeZoom: 19, maxZoom: 19, attribution: '&copy; OpenStreetMap' }
            );

            satellite.addTo(map);
            L.control.scale({ imperial: false, metric: true, position: 'bottomright' }).addTo(map);

            // 3. Satellite/Standard toggle
            const btnSat = document.getElementById('btnSatellite');
            const btnStd = document.getElementById('btnStandard');

            function activate(which) {
                if (which === 'sat') {
                    map.removeLayer(standard);
                    satellite.addTo(map);
                    btnSat.style.background = 'var(--accent)'; btnSat.style.color = '#fff';
                    btnStd.style.background = 'transparent'; btnStd.style.color = 'var(--text-secondary)';
                } else {
                    map.removeLayer(satellite);
                    standard.addTo(map);
                    btnStd.style.background = 'var(--accent)'; btnStd.style.color = '#fff';
                    btnSat.style.background = 'transparent'; btnSat.style.color = 'var(--text-secondary)';
                }
            }
            btnSat.addEventListener('click', () => activate('sat'));
            btnStd.addEventListener('click', () => activate('std'));

            // 4. Data
            const userPins   = @json($userPins);
            const wantedPins = @json($wantedPins);
            const equipmentPins = @json($equipmentPins);

            console.log('userPins:', userPins.length, '| wantedPins:', wantedPins.length);

            // 5. Role colors
            const roleColors = {
                'admin':        '#d97706',
                'merchant':     '#7c3aed',
                'ghost_hunter': '#059669',
                'civilian':     '#0284c7',
            };

            // 6. Marker icon builder
            function makeIcon(color) {
                const svg =
                    '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="34" viewBox="0 0 26 34">' +
                    '<path d="M13 0C5.82 0 0 5.82 0 13c0 9.75 13 21 13 21s13-11.25 13-21c0-7.18-5.82-13-13-13z" fill="' + color + '" stroke="#ffffff" stroke-width="2"/>' +
                    '<circle cx="13" cy="13" r="4.5" fill="#ffffff"/>' +
                    '</svg>';

                return L.divIcon({
                    html: svg,
                    className: 'custom-marker',
                    iconSize:    [26, 34],
                    iconAnchor:  [13, 34],
                    popupAnchor: [0, -34],
                });
            }

            // Return a small inline SVG badge for merchant / hunter roles
                   // Return a small inline SVG badge for merchant / hunter roles
            function badgeSvg(role) {
                if (role === 'merchant') {
                    return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-left:4px; margin-top:-2px;"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>';
                }
                if (role === 'ghost_hunter') {
                    return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-left:4px; margin-top:-2px;"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>';
                }
                return '';
            }

            // 7. Track markers + coords
            const allUserMarkers   = [];
            const allWantedMarkers = [];
            const allCoords        = [];

            // 8. User pins
            userPins.forEach(function (u) {
                const lat = parseFloat(u.lat);
                const lng = parseFloat(u.lng);

                if (isNaN(lat) || isNaN(lng)) return;

                const color  = roleColors[u.role] || '#64748b';
                const marker = L.marker([lat, lng], { icon: makeIcon(color) });

                marker.addTo(map);
                marker.bindPopup(
                    '<div style="font-family:sans-serif;min-width:180px;">' +
                        '<div style="font-size:10.5px;font-weight:600;color:' + color + ';text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">' + u.role_label + '</div>' +
                        '<div style="font-weight:600;font-size:15px;color:#0f172a;margin-bottom:4px;display:flex;align-items:center;gap:4px;">' + u.name + badgeSvg(u.role) + '</div>' +
                        (u.location_name ? '<div style="font-size:12px;color:#64748b;">📍 ' + u.location_name + '</div>' : '') +
                    '</div>'
                );

                marker.searchName = u.name.toLowerCase();
                marker.userRole   = u.role;

                allUserMarkers.push(marker);
                allCoords.push([lat, lng]);
            });

            // 9. Wanted pins
            wantedPins.forEach(function (w) {
                const lat = parseFloat(w.lat);
                const lng = parseFloat(w.lng);

                if (isNaN(lat) || isNaN(lng)) return;

                const marker = L.marker([lat, lng], { icon: makeIcon('#d97706') });

                marker.addTo(map);
                marker.bindPopup(
                    '<div style="font-family:sans-serif;min-width:200px;">' +
                        '<div style="font-size:10.5px;font-weight:600;color:#d97706;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Wanted Ad</div>' +
                        '<div style="font-weight:600;font-size:15px;color:#0f172a;margin-bottom:4px;">' + w.gear_name + '</div>' +
                        '<div style="font-size:12px;color:#64748b;margin-bottom:2px;">' + w.quantity + '× at ' + w.price_per_unit + ' each</div>' +
                        '<div style="font-size:11.5px;color:#94a3b8;display:flex;align-items:center;gap:4px;">by ' + w.poster_name + badgeSvg(w.poster_role_key) + '</div>' +
                        '<a href="' + w.url + '" style="display:inline-block;margin-top:10px;background:#0f172a;color:#fff;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:500;text-decoration:none;">View on Marketplace</a>' +
                    '</div>'
                );

                marker.searchName = w.gear_name.toLowerCase();

                allWantedMarkers.push(marker);
                allCoords.push([lat, lng]);
            });


            // Deployed equipment is rendered on this same Haunt Map.
            const equipmentMarkers = [];
            const equipmentColors = { ward: '#2563eb', anchor: '#7c3aed', trap: '#dc2626', scanner: '#0891b2' };
            equipmentPins.forEach(function (device) {
                const lat = parseFloat(device.lat);
                const lng = parseFloat(device.lng);
                if (isNaN(lat) || isNaN(lng)) return;
                const color = equipmentColors[device.type] || '#64748b';
                const marker = L.marker([lat, lng], { icon: makeIcon(color) }).addTo(map);
                const circle = L.circle([lat, lng], {
                    radius: device.radius,
                    color: device.powered ? color : '#64748b',
                    fillColor: device.powered ? color : '#64748b',
                    fillOpacity: device.powered ? 0.12 : 0.04,
                    weight: 2,
                    dashArray: device.powered ? null : '5 6'
                }).addTo(map);
                marker.bindPopup(
                    '<div style="min-width:190px;font-family:sans-serif;">' +
                    '<strong>' + escapeHtml(device.name) + '</strong><br>' +
                    '<span style="font-size:12px;color:#64748b;">' + escapeHtml(device.type) + ' · deployed by ' + escapeHtml(device.owner) + '</span><br>' +
                    '<span style="font-size:12px;">' + (device.powered ? 'Active · battery ' + device.battery + '%' : 'Inactive / out of battery') + '</span><br>' +
                    '<span style="font-size:12px;">Radius: ' + device.radius + ' m</span>' +
                    '</div>'
                );
                marker.searchName = device.name.toLowerCase();
                equipmentMarkers.push(marker);
                allCoords.push([lat, lng]);
            });

            function escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, function (character) {
                    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character];
                });
            }

            // Click the map to set deployment coordinates in the form above.
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const coordinates = document.getElementById('pickedCoordinates');
            let placementMarker = null;
            if (latInput && lngInput && coordinates) {
                function choosePlacement(latlng) {
                    latInput.value = latlng.lat.toFixed(7);
                    lngInput.value = latlng.lng.toFixed(7);
                    coordinates.textContent = 'Selected: ' + latlng.lat.toFixed(5) + ', ' + latlng.lng.toFixed(5);
                    if (placementMarker) map.removeLayer(placementMarker);
                    placementMarker = L.marker(latlng, { draggable: true }).addTo(map).bindPopup('New equipment placement');
                    placementMarker.on('dragend', function (event) { choosePlacement(event.target.getLatLng()); });
                }
                map.on('click', function (event) { choosePlacement(event.latlng); });
            }


            // 10. Filters + search state
            const filterBtns    = document.querySelectorAll('.map-filter');
            const activeFilters = new Set(['users', 'wanted', 'merchants']);
            let   searchQuery   = '';

            function applyVisibility() {
                const showUsers     = activeFilters.has('users');
                const showMerchants = activeFilters.has('merchants');
                const showWanted    = activeFilters.has('wanted');

                allUserMarkers.forEach(marker => {
                    const isMerchant  = marker.userRole === 'merchant';
                    const roleAllowed = showUsers || (!showUsers && showMerchants && isMerchant);
                    const searchMatch = !searchQuery || marker.searchName.includes(searchQuery);
                    const shouldShow  = roleAllowed && searchMatch;

                    if (shouldShow && !map.hasLayer(marker))       marker.addTo(map);
                    else if (!shouldShow && map.hasLayer(marker))  map.removeLayer(marker);
                });

                allWantedMarkers.forEach(marker => {
                    const searchMatch = !searchQuery || marker.searchName.includes(searchQuery);
                    const shouldShow  = showWanted && searchMatch;

                    if (shouldShow && !map.hasLayer(marker))       marker.addTo(map);
                    else if (!shouldShow && map.hasLayer(marker))  map.removeLayer(marker);
                });

                const visible = allUserMarkers.filter(m => map.hasLayer(m)).length
                              + allWantedMarkers.filter(m => map.hasLayer(m)).length;
                const total   = allUserMarkers.length + allWantedMarkers.length;

                const emptyEl = document.getElementById('emptyState');
                if (emptyEl) {
                    emptyEl.style.display = (total === 0 || visible === 0) ? 'block' : 'none';
                }
            }

            // 11. Filter button listeners
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    const filter = this.dataset.filter;
                    if (activeFilters.has(filter)) {
                        activeFilters.delete(filter);
                        this.classList.remove('active');
                    } else {
                        activeFilters.add(filter);
                        this.classList.add('active');
                    }
                    applyVisibility();
                });
            });

            // 12. Search input
            const searchInput = document.getElementById('mapSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    searchQuery = this.value.toLowerCase().trim();
                    applyVisibility();
                });
            }

            // 13. Auto-fit bounds
            if (allCoords.length === 1) {
                map.setView(allCoords[0], 13);
            } else if (allCoords.length > 1) {
                map.fitBounds(allCoords, { padding: [60, 60], maxZoom: 13 });
            }

            // 14. Re-render Lucide icons
            if (window.lucide) lucide.createIcons();

        });
    </script>

@endsection