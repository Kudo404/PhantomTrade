@extends('layouts.app')

@section('title', 'Set My Location — Phantom Trade')

@section('content')

    <h1 class="text-[#a78bfa] mt-2 mr-0 mb-1 ml-0">Set My Location</h1>
    <p class="text-[#888] mb-5">
        Click on the map to drop your pin, or use your current location. We'll automatically detect the place name.
    </p>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />

    <form method="POST" action="/profile/location" class="bg-[#2a2a45] pt-6 pr-6 pb-6 pl-6 rounded-[12px] max-w-[820px] [border:1px_solid_#3a3a5c]"
         >
        @csrf

        {{-- Map --}}
        <div class="rounded-[10px] overflow-hidden [border:1px_solid_#3a3a5c] mb-4">
            <div id="mapPicker" class="h-[400px] w-full bg-[#1a1a2e]"></div>
        </div>

        {{-- Hidden coordinate inputs --}}
        <input type="hidden" name="latitude"  id="latInput" value="{{ old('latitude',  $user->latitude  ?? '') }}">
        <input type="hidden" name="longitude" id="lngInput" value="{{ old('longitude', $user->longitude ?? '') }}">

        @error('latitude')  <div class="text-[#fca5a5] text-[13px]">{{ $message }}</div> @enderror
        @error('longitude') <div class="text-[#fca5a5] text-[13px]">{{ $message }}</div> @enderror

        {{-- Location name --}}
        <label class="block text-[13px] text-[#aaa] mt-3.5 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
            Location name
        </label>
        <div class="flex gap-2">
            <input type="text" name="location_name" id="locationNameInput"
                   value="{{ old('location_name', $user->location_name) }}"
                   placeholder="Click on the map to auto-detect" class="flex-1 pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee]"
                  >
            <button type="button" onclick="regenerateName()"
                    title="Re-detect from pin" class="bg-[#3a3a5c] text-[#c084fc] [border:1px_solid_#a78bfa] pt-0 pr-3.5 pb-0 pl-3.5 rounded-md font-bold text-[12px] cursor-pointer"
                   >
                Re-detect
            </button>
        </div>
        <input type="hidden" name="regenerate_name" id="regenerateInput" value="0">
        @error('location_name') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

        {{-- Coordinates preview --}}
        <div class="mt-3 text-[12px] text-[#666]">
            Selected:
            <span id="coordPreview" class="text-[#c084fc] font-semibold">
                @if ($user->hasLocation())
                    {{ $user->latitude }}, {{ $user->longitude }}
                @else
                    (click the map)
                @endif
            </span>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2.5 mt-5">
            <button type="submit" class="btn">Save Location</button>
            <button type="button" onclick="useMyLocation()" class="btn bg-[#3a3a5c] text-[#eee]">
                Use My Current Location
            </button>
        </div>
    </form>

    {{-- Leaflet JS --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const savedLat  = {{ $user->latitude  ?? 'null' }};
            const savedLng  = {{ $user->longitude ?? 'null' }};
            const startLat  = savedLat ?? 8.5023;
            const startLng  = savedLng ?? 125.9732;
            const startZoom = savedLat ? 14 : 12;

            const map = L.map('mapPicker', {
                center: [startLat, startLng],
                zoom: startZoom,
                minZoom: 3,
                maxZoom: 19,
            });

            L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                { maxNativeZoom: 17, maxZoom: 19, attribution: 'Tiles &copy; Esri' }
            ).addTo(map);

            let marker = null;

            function placeMarker(lat, lng, shouldLookup) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', () => {
                        const pos = marker.getLatLng();
                        updateCoords(pos.lat, pos.lng);
                        lookupName(pos.lat, pos.lng);
                    });
                }
                updateCoords(lat, lng);

                if (shouldLookup) {
                    lookupName(lat, lng);
                }
            }

            function updateCoords(lat, lng) {
                document.getElementById('latInput').value = lat.toFixed(7);
                document.getElementById('lngInput').value = lng.toFixed(7);
                document.getElementById('coordPreview').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
            }

            function lookupName(lat, lng) {
                const input = document.getElementById('locationNameInput');
                input.value = 'Detecting...';
                input.disabled = true;

                // Nominatim reverse geocode from the browser (no server round trip)
                const url = 'https://nominatim.openstreetmap.org/reverse?format=json&zoom=18&addressdetails=1&lat=' + lat + '&lon=' + lng;

                fetch(url, {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(r => r.json())
                    .then(data => {
                        input.disabled = false;
                                               if (data && data.address) {
                            const a = data.address;
                            const parts = [
                                a.neighbourhood || null,
                                a.suburb        || null,
                                a.village       || a.town || a.city || a.municipality || null,
                                a.county        || null,
                                a.state         || null,
                                a.country       || null,
                            ].filter(Boolean);

                            // De-duplicate
                            const unique = parts.filter((v, i, arr) => arr.indexOf(v) === i);

                            // Keep first 3 parts
                            input.value = unique.slice(0, 3).join(', ') || (data.display_name || '');
                        } else {
                            input.value = '';
                            input.placeholder = 'Could not detect — type manually';
                        }
                    })
                    .catch(() => {
                        input.disabled = false;
                        input.value = '';
                        input.placeholder = 'Could not detect — type manually';
                    });
            }

            if (savedLat && savedLng) {
                placeMarker(savedLat, savedLng, false);
            }

            map.on('click', function (e) {
                placeMarker(e.latlng.lat, e.latlng.lng, true);
            });

            window.useMyLocation = function () {
                if (! navigator.geolocation) {
                    alert('Your browser does not support geolocation.');
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        map.setView([lat, lng], 15);
                        placeMarker(lat, lng, true);
                    },
                    (err) => alert('Could not get your location: ' + err.message)
                );
            };

            window.regenerateName = function () {
                const lat = parseFloat(document.getElementById('latInput').value);
                const lng = parseFloat(document.getElementById('lngInput').value);
                if (isNaN(lat) || isNaN(lng)) {
                    alert('Please click the map first to drop a pin.');
                    return;
                }
                document.getElementById('regenerateInput').value = '1';
                lookupName(lat, lng);
            };
        });
    </script>

@endsection