@extends('layouts.app')

@section('title', 'Spectral Protection — PhantomTrade')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">

<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-[var(--accent)]">Civilian safety & field equipment</p>
        <h1 class="text-2xl font-bold text-[var(--text)]">Spectral Protection Map</h1>
        <p class="mt-1 max-w-2xl text-sm text-[var(--text-secondary)]">
            Place equipment you own on the map. Powered wards protect a circular area until their battery runs out.
        </p>
    </div>
    <a href="/gears" class="btn">Buy gear & batteries</a>
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
@endif

<div class="mb-5 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1.6fr)_minmax(300px,1fr)]">
    <section class="overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface)]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--border)] p-4">
            <div>
                <h2 class="font-semibold text-[var(--text)]">Choose a placement point</h2>
                <p class="text-xs text-[var(--text-secondary)]">Click the map to place a marker. You choose the location manually.</p>
            </div>
            <button type="button" id="locateMe" class="btn btn-secondary text-xs">Use my current location</button>
        </div>
        <div id="protectionMap" class="h-[460px] w-full bg-slate-100"></div>
        <div class="border-t border-[var(--border)] p-3 text-xs text-[var(--text-secondary)]">
            Blue circles show powered protection zones. Grey circles are inactive or out of battery.
        </div>
    </section>

    <section class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
        <h2 class="mb-1 text-lg font-semibold text-[var(--text)]">Deploy equipment</h2>
        <p class="mb-5 text-sm text-[var(--text-secondary)]">
            @if ($user->isCivilian())
                Civilians can deploy Spirit Wards to protect a home or other chosen location. Hunting remains restricted to licensed hunters.
            @else
                Choose an owned field device to create its protection zone.
            @endif
        </p>

        @if ($owned->isEmpty())
            <div class="rounded-lg border border-dashed border-[var(--border-strong)] bg-[var(--surface-soft)] p-4 text-sm">
                <p class="mb-2 font-semibold text-[var(--text)]">No deployable gear in your inventory</p>
                <p class="mb-4 text-[var(--text-secondary)]">Buy a Spirit Ward from the Gear Shop, then return here to place it on the map.</p>
                <a href="/gears" class="btn">Browse Gear Shop</a>
            </div>
        @else
            <form method="POST" action="/protection/deploy" id="deployForm" class="space-y-4">
                @csrf
                <div>
                    <label for="gear_id" class="mb-1 block text-sm font-medium text-[var(--text)]">Equipment you own</label>
                    <select id="gear_id" name="gear_id" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] p-3 text-sm text-[var(--text)]">
                        <option value="">Choose equipment…</option>
                        @foreach ($owned as $item)
                            <option value="{{ $item->gear->id }}" data-type="{{ $item->gear->type }}">
                                {{ $item->gear->name }} ({{ $item->quantity }} owned)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="label" class="mb-1 block text-sm font-medium text-[var(--text)]">Location name (optional)</label>
                    <input id="label" name="label" maxlength="80" placeholder="e.g. Home / Front entrance" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] p-3 text-sm text-[var(--text)]">
                </div>
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
                <div id="pickedCoordinates" class="rounded-lg bg-[var(--surface-soft)] p-3 text-xs text-[var(--text-secondary)]">
                    No location selected yet. Click anywhere on the map.
                </div>
                <div class="rounded-lg border border-[var(--border)] p-3 text-xs text-[var(--text-secondary)]">
                    <p class="font-semibold text-[var(--text)]">Battery & range</p>
                    <p class="mt-1">Each deployment starts with a full battery lasting up to 24 hours. A Spirit Ward covers a 100-metre radius. You can replace its battery using a battery item bought from the Gear Shop.</p>
                </div>
                <button type="submit" class="btn w-full">Place equipment on map</button>
            </form>
        @endif
    </section>
</div>

<section class="rounded-xl border border-[var(--border)] bg-[var(--surface)]">
    <div class="border-b border-[var(--border)] p-4">
        <h2 class="font-semibold text-[var(--text)]">Your deployed devices</h2>
        <p class="text-xs text-[var(--text-secondary)]">Battery status is calculated from each device's activation time.</p>
    </div>
    @if ($devices->isEmpty())
        <div class="p-8 text-center text-sm text-[var(--text-secondary)]">
            No devices deployed yet. Choose equipment and click a location on the map to get started.
        </div>
    @else
        <div class="divide-y divide-[var(--border)]">
            @foreach ($devices as $device)
                <div class="flex flex-col gap-4 p-4 md:flex-row md:items-center">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $device->powered ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                        <i data-lucide="{{ $device->gear->type === 'ward' ? 'shield-check' : 'radio-tower' }}" class="h-5 w-5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-[var(--text)]">{{ $device->label ?: $device->gear->name }}</div>
                        <div class="text-xs text-[var(--text-secondary)]">{{ ucfirst($device->gear->type) }} · {{ $device->radius_meters }} m protection radius</div>
                        <div class="mt-2 h-2 max-w-sm overflow-hidden rounded-full bg-[var(--border)]">
                            <div class="h-full rounded-full {{ $device->battery_percent > 30 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $device->battery_percent }}%"></div>
                        </div>
                        <div class="mt-1 text-xs {{ $device->powered ? 'text-[var(--text-secondary)]' : 'font-semibold text-red-600' }}">
                            @if ($device->powered)
                                Battery {{ $device->battery_percent }}% · about {{ floor($device->remaining_minutes / 60) }}h {{ $device->remaining_minutes % 60 }}m left
                            @else
                                Out of battery — this device is not protecting its area.
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($user->ecto >= 10)
                            <form method="POST" action="/protection/{{ $device->id }}/recharge">
                                @csrf
                                <button class="btn btn-secondary text-xs" type="submit">Recharge · 10 Ecto</button>
                            </form>
                        @else
                            <span class="self-center text-xs text-[var(--text-secondary)]">Need 10 Ecto to recharge</span>
                        @endif
                        @if ($batteries->sum('quantity') > 0)
                            <form method="POST" action="/protection/{{ $device->id }}/battery">
                                @csrf
                                <button class="btn btn-secondary text-xs" type="submit">Use spare battery</button>
                            </form>
                        @else
                            <a href="/gears?type=battery" class="btn btn-secondary text-xs">Buy battery</a>
                        @endif
                        @if ($device->is_active)
                            <form method="POST" action="/protection/{{ $device->id }}/deactivate">
                                @csrf
                                <button class="btn btn-secondary text-xs" type="submit">Switch off</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('protectionMap').setView([12.8797, 121.7740], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const deployed = @json($devices->map(fn ($d) => [
        'name' => $d->label ?: $d->gear->name,
        'type' => $d->gear->type,
        'lat' => (float) $d->latitude,
        'lng' => (float) $d->longitude,
        'radius' => (int) $d->radius_meters,
        'powered' => (bool) $d->powered,
        'battery' => (int) $d->battery_percent,
    ])->values());

    deployed.forEach(function (device) {
        const color = device.powered ? '#2563eb' : '#64748b';
        L.circle([device.lat, device.lng], {
            radius: device.radius,
            color: color,
            fillColor: color,
            fillOpacity: device.powered ? 0.16 : 0.06,
            weight: 2,
            dashArray: device.powered ? null : '5 6'
        }).addTo(map).bindPopup(
            '<strong>' + escapeHtml(device.name) + '</strong><br>' +
            (device.powered ? 'Powered · battery ' + device.battery + '%' : 'Inactive · no protection') +
            '<br>Radius: ' + device.radius + ' m'
        );
        L.marker([device.lat, device.lng]).addTo(map).bindPopup(escapeHtml(device.name));
    });

    let placementMarker = null;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const coordinates = document.getElementById('pickedCoordinates');

    function selectLocation(latlng) {
        latInput.value = latlng.lat.toFixed(7);
        lngInput.value = latlng.lng.toFixed(7);
        coordinates.textContent = 'Selected location: ' + latlng.lat.toFixed(5) + ', ' + latlng.lng.toFixed(5);
        if (placementMarker) map.removeLayer(placementMarker);
        placementMarker = L.marker(latlng, { draggable: true }).addTo(map).bindPopup('New device placement').openPopup();
        placementMarker.on('dragend', function (event) { selectLocation(event.target.getLatLng()); });
    }

    map.on('click', function (event) { selectLocation(event.latlng); });

    const locateButton = document.getElementById('locateMe');
    locateButton.addEventListener('click', function () {
        if (!navigator.geolocation) {
            coordinates.textContent = 'Your browser does not support location. Click the map to choose a point.';
            return;
        }
        locateButton.disabled = true;
        navigator.geolocation.getCurrentPosition(function (position) {
            const point = L.latLng(position.coords.latitude, position.coords.longitude);
            map.setView(point, 16);
            selectLocation(point);
            locateButton.disabled = false;
        }, function () {
            coordinates.textContent = 'Location permission was not granted. You can still click the map to choose a point.';
            locateButton.disabled = false;
        }, { enableHighAccuracy: false, timeout: 10000 });
    });

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (character) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character];
        });
    }
});
</script>
@endsection
