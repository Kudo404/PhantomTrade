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

    {{-- FIELD EQUIPMENT / DEPLOYMENT --}}
    <section class="field-panel mb-6 overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)]">
        <div class="field-panel__header flex flex-wrap items-center justify-between gap-4 p-5 md:p-6">
            <div class="flex items-start gap-3">
                <div class="field-panel__icon flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"><i data-lucide="radio-tower" class="h-6 w-6"></i></div>
                <div>
                    <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.16em] text-[var(--accent)]">Field operations</p>
                    <h2 class="text-lg font-bold tracking-tight text-[var(--text)] md:text-xl">Deploy equipment</h2>
                    <p class="mt-1 max-w-2xl text-sm leading-6 text-[var(--text-secondary)]">Choose a device, click anywhere on the Haunt Map to set its position, then deploy. Everything stays on this map.</p>
                </div>
            </div>
            <a href="/gears" class="btn inline-flex items-center gap-2"><i data-lucide="shopping-bag" class="h-4 w-4"></i> Gear Shop <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i></a>
        </div>
        <div class="border-t border-[var(--border)] p-5 md:p-6">
            @if ($owned->isEmpty())
                <div class="flex flex-col gap-4 rounded-xl border border-dashed border-[var(--border)] bg-[var(--surface-soft)] p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <i data-lucide="package-open" class="mt-0.5 h-5 w-5 shrink-0 text-[var(--text-muted)]"></i>
                        <div><p class="font-semibold text-[var(--text)]">No deployable gear in your inventory</p><p class="mt-1 text-sm text-[var(--text-secondary)]">Pick up a ward, anchor, trap, or scanner from the Gear Shop and it will appear here.</p></div>
                    </div>
                    <a href="/gears" class="btn shrink-0">Browse equipment</a>
                </div>
            @else
                <form method="POST" action="/protection/deploy" id="deployForm" class="grid grid-cols-1 items-end gap-4 lg:grid-cols-[minmax(190px,1fr)_minmax(180px,1fr)_minmax(180px,1fr)_auto]">
                    @csrf
                    <div class="min-w-0">
                        <label for="gear_id" class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[var(--text-secondary)]"><i data-lucide="cpu" class="h-3.5 w-3.5"></i> Equipment</label>
                        <select id="gear_id" name="gear_id" required class="field-control w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm text-[var(--text)]">
                            <option value="">Choose owned equipment...</option>
                            @foreach ($owned as $item)
                                <option value="{{ $item->gear->id }}">{{ $item->gear->name }} · {{ $item->quantity }} owned</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0">
                        <label for="label" class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-[var(--text-secondary)]"><i data-lucide="map-pin" class="h-3.5 w-3.5"></i> Placement label <span class="font-normal normal-case tracking-normal">(optional)</span></label>
                        <input id="label" name="label" maxlength="80" placeholder="e.g. North entrance" class="field-control w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] p-3 text-sm text-[var(--text)]">
                    </div>
                    <input type="hidden" id="latitude" name="latitude" required>
                    <input type="hidden" id="longitude" name="longitude" required>
                    <div id="pickedCoordinates" class="coordinate-status rounded-xl border border-dashed border-[var(--border)] bg-[var(--surface-soft)] p-3 text-xs leading-5 text-[var(--text-secondary)]"><span class="mb-1 flex items-center gap-1.5 font-bold uppercase tracking-wide"><i data-lucide="crosshair" class="h-3.5 w-3.5"></i> Location pending</span>Click the map below to choose a deployment point.</div>
                    <button type="submit" class="btn inline-flex min-h-[46px] items-center justify-center gap-2 whitespace-nowrap rounded-xl px-5"><i data-lucide="send" class="h-4 w-4"></i> Deploy device</button>
                </form>
            @endif
            <div class="mt-5 flex items-start gap-2 rounded-lg bg-[var(--surface-soft)] px-3 py-2.5 text-xs leading-5 text-[var(--text-secondary)]"><i data-lucide="info" class="mt-0.5 h-4 w-4 shrink-0"></i><p>All signed-in roles can deploy equipment they own. Deploying devices does not unlock hunting; hunting permissions remain unchanged.</p></div>
        </div>
    </section>

    {{-- TACTICAL RADAR STATUS --}}
    <section class="radar-console mb-4 grid grid-cols-2 gap-3 rounded-2xl p-4 sm:grid-cols-4" aria-label="Radar status">
        <div class="radar-console__heading col-span-2 flex items-center justify-between sm:col-span-4">
            <div class="flex items-center gap-2"><span class="radar-live-dot"></span><span class="text-[11px] font-bold uppercase tracking-[.18em]">Field sensor network</span></div>
            <span class="text-[10px] uppercase tracking-widest opacity-60">PHANTOM // LIVE MAP</span>
        </div>
        <div class="radar-stat"><span>Mapped users</span><strong id="radarUserCount">—</strong></div>
        <div class="radar-stat"><span>Wanted listings</span><strong id="radarWantedCount">—</strong></div>
        <div class="radar-stat"><span>Deployed devices</span><strong id="radarDeviceCount">—</strong></div>
        <div class="radar-stat"><span>Powered sensors</span><strong id="radarActiveCount">—</strong></div>
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
        .field-panel { box-shadow: 0 12px 32px rgba(15, 23, 42, 0.055); }
        .field-panel__header { background: linear-gradient(115deg, var(--surface) 0%, var(--surface-soft) 100%); }
        .field-panel__icon { color: var(--accent); background: color-mix(in srgb, var(--accent) 11%, transparent); border: 1px solid color-mix(in srgb, var(--accent) 22%, transparent); }
        .field-panel .field-control { min-height: 46px; outline: none; transition: border-color .15s ease, box-shadow .15s ease; }
        .field-panel .field-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 15%, transparent); }
        .coordinate-status { min-height: 46px; }
        .field-panel button[type="submit"] { box-shadow: 0 4px 12px color-mix(in srgb, var(--accent) 18%, transparent); }
        @media (max-width: 767px) { #map { height: 520px; } .field-panel__header { align-items: flex-start; } #deployForm button[type="submit"] { width: 100%; } }
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

        .radar-console { color: #d7f7ff; background: radial-gradient(ellipse at 8% 0%, rgba(8,145,178,.18), transparent 48%), linear-gradient(125deg,#071521,#0b1e2b 60%,#0a1723); border: 1px solid rgba(34,211,238,.22); box-shadow: inset 0 1px rgba(255,255,255,.035), 0 10px 28px rgba(2,8,23,.12); }
        .radar-console__heading { color: #67e8f9; padding-bottom: 3px; }
        .radar-live-dot { width: 7px; height: 7px; display: inline-block; border-radius: 50%; background: #34d399; box-shadow: 0 0 0 4px rgba(52,211,153,.12), 0 0 12px #34d399; animation: radarLive 1.8s ease-in-out infinite; }
        .radar-stat { min-width: 0; padding: 12px; border-radius: 11px; background: rgba(148,211,231,.055); border: 1px solid rgba(148,211,231,.1); }
        .radar-stat span { display:block; color:#8baabd; font-size:10px; text-transform:uppercase; letter-spacing:.09em; line-height:1.5; }
        .radar-stat strong { display:block; margin-top:5px; color:#e6fbff; font-size:25px; font-weight:700; line-height:1.1; font-variant-numeric:tabular-nums; }
        @keyframes radarLive { 50% { opacity:.45; box-shadow:0 0 0 7px rgba(52,211,153,.04),0 0 5px #34d399; } }
        @media (prefers-reduced-motion: reduce) { .radar-live-dot { animation:none; } }

        /* Animated field-radar effects for deployed equipment */
        .radar-marker { background: transparent !important; border: 0 !important; }
        .radar-marker__core { position: relative; width: 42px; height: 42px; display: grid; place-items: center; color: var(--radar-color, #06b6d4); }
        .radar-marker__core::before, .radar-marker__core::after, .radar-marker__ring {
            content: ""; position: absolute; inset: 5px; border: 1.5px solid currentColor; border-radius: 50%;
            pointer-events: none;
        }
        .radar-marker__ring { animation: radarBreath 3s ease-out infinite; }
        .radar-marker__ring--two { animation-delay: 1s; }
        .radar-marker__ring--three { animation-delay: 2s; }
        .radar-marker__core::before { inset: 11px; background: color-mix(in srgb, currentColor 20%, transparent); box-shadow: 0 0 12px currentColor, inset 0 0 8px currentColor; }
        .radar-marker__core::after { inset: 16px; background: #e6fdff; border: 2px solid currentColor; box-shadow: 0 0 12px currentColor; }
        .radar-marker__sweep { position: absolute; inset: 1px; border-radius: 50%; overflow: hidden; opacity: .75; }
        .radar-marker__sweep::before { content: ""; position: absolute; inset: 0; border-radius: 50%; background: conic-gradient(from 0deg, transparent 0deg 275deg, color-mix(in srgb, currentColor 45%, transparent) 330deg, transparent 360deg); animation: radarSweep 4s linear infinite; }
        .radar-marker--offline { filter: grayscale(.8); opacity: .72; }
        .radar-radius { animation: radiusPulse 3.2s ease-in-out infinite; }
        @keyframes radarBreath { 0% { transform: scale(.35); opacity: .9; } 75%,100% { transform: scale(2.4); opacity: 0; } }
        @keyframes radarSweep { to { transform: rotate(360deg); } }
        @keyframes radiusPulse { 0%,100% { fill-opacity: .06; } 50% { fill-opacity: .15; } }
        @media (prefers-reduced-motion: reduce) {
            .radar-marker__ring, .radar-marker__sweep::before, .radar-radius { animation: none !important; }
            .radar-marker__ring { opacity: .25; transform: scale(1.7); }
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


            // Deployed equipment: live-radius rings + animated radar beacon.
            const equipmentMarkers = [];
            const equipmentRadii = [];
            const equipmentColors = { ward: '#22d3ee', anchor: '#a78bfa', trap: '#fb7185', scanner: '#38bdf8' };

            function makeRadarIcon(color, powered) {
                const offlineClass = powered ? '' : ' radar-marker--offline';
                return L.divIcon({
                    className: 'radar-marker' + offlineClass,
                    html: '<div class="radar-marker__core" style="--radar-color:' + color + '">' +
                        (powered ? '<span class="radar-marker__sweep"></span>' : '') +
                        (powered ? '<span class="radar-marker__ring"></span><span class="radar-marker__ring radar-marker__ring--two"></span><span class="radar-marker__ring radar-marker__ring--three"></span>' : '') +
                        '</div>',
                    iconSize: [42, 42],
                    iconAnchor: [21, 21],
                    popupAnchor: [0, -19]
                });
            }

            equipmentPins.forEach(function (device) {
                const lat = parseFloat(device.lat);
                const lng = parseFloat(device.lng);
                const radius = Math.max(0, parseFloat(device.radius) || 0);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

                const color = equipmentColors[device.type] || '#38bdf8';
                const marker = L.marker([lat, lng], {
                    icon: makeRadarIcon(color, !!device.powered),
                    zIndexOffset: 800
                }).addTo(map);

                const circle = L.circle([lat, lng], {
                    radius: radius,
                    className: device.powered ? 'radar-radius' : '',
                    color: device.powered ? color : '#64748b',
                    fillColor: device.powered ? color : '#64748b',
                    fillOpacity: device.powered ? 0.09 : 0.035,
                    opacity: device.powered ? 0.8 : 0.45,
                    weight: device.powered ? 2 : 1,
                    dashArray: device.powered ? '7 7' : '4 7',
                    interactive: false
                }).addTo(map);

                marker.bindPopup(
                    '<div style="min-width:205px;font-family:Inter,-apple-system,sans-serif;color:#e5f7ff;background:#071827;padding:3px;">' +
                    '<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px;">' +
                    '<strong style="font-size:14px;color:#f0fbff;">' + escapeHtml(device.name) + '</strong>' +
                    '<span style="font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:' + (device.powered ? '#34d399' : '#94a3b8') + ';">' + (device.powered ? '● ACTIVE' : '○ OFFLINE') + '</span></div>' +
                    '<div style="font-size:12px;color:#8baabd;margin-bottom:9px;">' + escapeHtml(device.type) + ' · deployed by ' + escapeHtml(device.owner) + '</div>' +
                    '<div style="display:flex;justify-content:space-between;gap:10px;font-size:12px;margin-bottom:8px;"><span style="color:#67e8f9;">Battery</span><strong>' + device.battery + '%</strong></div>' +
                    '<div style="height:5px;border-radius:99px;background:#173449;overflow:hidden;margin-bottom:10px;"><div style="height:100%;width:' + Math.max(0, Math.min(100, Number(device.battery) || 0)) + '%;background:linear-gradient(90deg,#0891b2,#2dd4bf);border-radius:99px;"></div></div>' +
                    '<div style="border-top:1px solid #1b3a4d;padding-top:8px;font-size:12px;color:#b7d4e2;">⌁ Detection radius <strong style="color:#67e8f9;">' + radius + ' m</strong></div>' +
                    '</div>',
                    { className: 'radar-popup', closeButton: true }
                );

                marker.searchName = String(device.name).toLowerCase();
                marker.deviceType = device.type;
                equipmentMarkers.push(marker);
                equipmentRadii.push(circle);
                allCoords.push([lat, lng]);
            });

            // Populate the HUD from the map data already loaded by Laravel.
            const setHudCount = function (id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = String(value);
            };
            setHudCount('radarUserCount', userPins.length);
            setHudCount('radarWantedCount', wantedPins.length);
            setHudCount('radarDeviceCount', equipmentPins.length);
            setHudCount('radarActiveCount', equipmentPins.filter(function (device) { return !!device.powered; }).length);

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