
@extends('layouts.app')

@section('title', 'Gear Shop — Phantom Trade')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 10px 0;">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
        <div>
            <h1 style="margin:0 0 6px; font-size:26px; color:var(--text);">Gear Shop</h1>
            <p style="margin:0; color:var(--text-secondary); font-size:14px;">Browse and purchase equipment for your hunts.</p>
        </div>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input
                type="search"
                id="gearSearch"
                placeholder="Search gear..."
                aria-label="Search gear"
                style="padding:10px 12px; width:220px; max-width:100%; background:var(--surface); color:var(--text); border:1px solid var(--border); border-radius:8px;"
            >

            @if (auth()->user()->isAdmin())
                <a href="{{ url('/gears/create') }}" class="btn" style="text-decoration:none;">
                    + Add Gear
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div style="padding:12px 16px; margin-bottom:16px; color:#166534; background:#dcfce7; border-radius:8px;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="padding:12px 16px; margin-bottom:16px; color:#991b1b; background:#fee2e2; border-radius:8px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
        <a href="{{ url('/gears') }}"
           style="padding:8px 14px; border-radius:20px; text-decoration:none; border:1px solid var(--border); background:{{ !request('type') ? 'var(--accent)' : 'var(--surface)' }}; color:{{ !request('type') ? '#fff' : 'var(--text)' }};">
            All Gear
        </a>

        @foreach ($types as $type)
            <a href="{{ url('/gears') }}?type={{ urlencode($type) }}"
               style="padding:8px 14px; border-radius:20px; text-decoration:none; border:1px solid var(--border); background:{{ request('type') === $type ? 'var(--accent)' : 'var(--surface)' }}; color:{{ request('type') === $type ? '#fff' : 'var(--text)' }};">
                {{ ucfirst($type) }}
            </a>
        @endforeach
    </div>

    @if ($gears->count())
        <div style="overflow-x:auto; border:1px solid var(--border); border-radius:12px; background:var(--surface);">
            <table style="width:100%; min-width:720px; border-collapse:collapse; text-align:left;">
                <thead>
                    <tr style="background:var(--bg); color:var(--text-secondary); font-size:12px; text-transform:uppercase;">
                        <th style="padding:14px;">Gear</th>
                        <th style="padding:14px;">Type</th>
                        <th style="padding:14px;">Price</th>
                        <th style="padding:14px;">Energy</th>
                        <th style="padding:14px; text-align:right;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($gears as $gear)
                        <tr class="gear-row"
                            data-name="{{ strtolower($gear->name) }}"
                            data-description="{{ strtolower($gear->description) }}"
                            style="border-top:1px solid var(--border);">

                            <td style="padding:12px 14px;">
                                <div style="display:flex; align-items:center; gap:12px; min-width:240px;">
                                    @if ($gear->image_path)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($gear->image_path) }}"
                                            alt="{{ $gear->name }}"
                                            loading="lazy"
                                            style="width:76px; height:76px; flex-shrink:0; object-fit:cover; border-radius:10px; border:1px solid var(--border); background:var(--bg);"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >
                                        <div style="display:none; width:76px; height:76px; flex-shrink:0; align-items:center; justify-content:center; border-radius:10px; background:var(--bg); color:var(--text-secondary);">
                                            ⚙
                                        </div>
                                    @else
                                        <div style="width:76px; height:76px; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:10px; background:var(--bg); color:var(--text-secondary); font-size:26px;">
                                            @switch($gear->type)
                                                @case('trap') 🎯 @break
                                                @case('ward') 🛡️ @break
                                                @case('anchor') ⚓ @break
                                                @case('scanner') 📡 @break
                                                @default ⚙️
                                            @endswitch
                                        </div>
                                    @endif

                                    <div style="min-width:0;">
                                        <a href="{{ url('/gears/'.$gear->id) }}"
                                           style="font-size:14px; font-weight:600; color:var(--text); text-decoration:none;">
                                            {{ $gear->name }}
                                        </a>

                                        <div style="margin-top:5px; font-size:12px; line-height:1.5; color:var(--text-secondary);">
                                            {{ \Illuminate\Support\Str::limit($gear->description, 75) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td style="padding:14px; color:var(--text-secondary);">
                                <span class="type-tag">{{ ucfirst($gear->type) }}</span>
                            </td>

                            <td style="padding:14px; white-space:nowrap; font-weight:600; color:var(--text);">
                                ₱ {{ number_format($gear->ecto_cost) }}
                            </td>

                            <td style="padding:14px; white-space:nowrap; color:var(--text-secondary);">
                                ⚡ {{ $gear->energy_cost }}
                            </td>

                            <td style="padding:14px;">
                                <div style="display:flex; justify-content:flex-end; align-items:center; gap:8px;">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ url('/gears/'.$gear->id) }}"
                                           title="View gear details"
                                           aria-label="View {{ $gear->name }}"
                                           style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1px solid var(--border); border-radius:8px; color:var(--text); text-decoration:none;">
                                            View
                                        </a>
                                    @endif

                                    <form method="GET" action="{{ url('/gears/'.$gear->id.'/checkout') }}" style="margin:0;">
                                        <button type="submit"
                                                title="Buy {{ $gear->name }}"
                                                style="padding:9px 12px; border:1px solid var(--border); border-radius:8px; background:var(--surface); color:var(--text); cursor:pointer;">
                                            Buy
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="noResults" style="display:none; padding:32px; text-align:center; color:var(--text-secondary);">
            No matching gear found. Try another search.
        </div>
    @else
        <div style="padding:48px 20px; text-align:center; border:1px dashed var(--border); border-radius:12px; background:var(--surface);">
            <div style="font-size:36px; margin-bottom:12px;">👻</div>
            <h2 style="margin:0 0 8px; color:var(--text);">No gear in the catalog</h2>
            <p style="margin:0 0 18px; color:var(--text-secondary);">
                @if (auth()->user()->isAdmin())
                    Add your first piece of hunting equipment.
                @else
                    Check back later for available equipment.
                @endif
            </p>

            @if (auth()->user()->isAdmin())
                <a href="{{ url('/gears/create') }}" class="btn" style="text-decoration:none;">
                    + Add Gear
                </a>
            @endif
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('gearSearch');
    const rows = document.querySelectorAll('.gear-row');
    const noResults = document.getElementById('noResults');

    if (!input) return;

    input.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const searchableText =
                (row.dataset.name || '') + ' ' +
                (row.dataset.description || '');

            const matches = searchableText.includes(query);

            row.style.display = matches ? '' : 'none';

            if (matches) visibleCount++;
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    });
});
</script>
@endsection
