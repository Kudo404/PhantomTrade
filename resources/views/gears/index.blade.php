@extends('layouts.app')

@section('title', 'Gear Shop — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Gear Shop
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Browse and purchase gear for your hunts.
            </p>
        </div>

        <div class="flex gap-2 flex-wrap items-center">
            <div class="relative">
                <i data-lucide="search" class="absolute left-[12px] top-[50%] [transform:translateY(-50%)] w-[15px] h-[15px] text-[var(--text-muted)] pointer-events-none"></i>
                <input type="text" id="gearSearch" placeholder="Search gear…" class="pt-2 pr-3 pb-2 pl-[34px] [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] outline-none w-[220px] [font-family:inherit]"
                      >
            </div>

            @if (auth()->user()->isAdmin())
                <a href="/gears/create" class="btn">
                    <i data-lucide="plus"></i>
                    <span>Add Gear</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ═══ FILTER CHIPS ═════════════════════════════ --}}
    <div class="flex gap-2 mb-5 flex-wrap">
        <a href="/gears" class="inline-flex items-center gap-1.5 pt-1.5 pr-3 pb-1.5 pl-3 text-[12.5px] font-medium rounded-[20px] no-underline {{ !request('type') ? '[border:1px_solid_var(--accent)]' : '[border:1px_solid_var(--border)]' }} {{ !request('type') ? 'bg-[var(--accent)]' : 'bg-[var(--surface)]' }} {{ !request('type') ? 'text-[#fff]' : 'text-[var(--text-secondary)]' }}"
          >
            <i data-lucide="layers" class="w-[13px] h-[13px]"></i>
            All
        </a>

        @foreach ($types as $type)
            @php $active = request('type') === $type; @endphp
            <a href="/gears?type={{ $type }}" class="inline-flex items-center gap-1.5 pt-1.5 pr-3 pb-1.5 pl-3 text-[12.5px] font-medium rounded-[20px] no-underline {{ $active ? '[border:1px_solid_var(--accent)]' : '[border:1px_solid_var(--border)]' }} {{ $active ? 'bg-[var(--accent)]' : 'bg-[var(--surface)]' }} {{ $active ? 'text-[#fff]' : 'text-[var(--text-secondary)]' }}"
              >
                <i data-lucide="{{ $type === 'trap' ? 'target' : ($type === 'ward' ? 'shield' : ($type === 'anchor' ? 'anchor' : 'scan-line')) }}" class="w-[13px] h-[13px]"></i>
                {{ ucfirst($type) }}
            </a>
        @endforeach
    </div>

    {{-- ═══ GEAR CATALOG ═════════════════════════════ --}}
    @if ($gears->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">

            {{-- Header row --}}
            <div class="grid [grid-template-columns:2.5fr_1fr_1fr_1fr_1.2fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Gear</div>
                <div>Type</div>
                <div>Cost</div>
                <div>Energy Cost</div>
                <div class="text-right">Actions</div>
            </div>

            @foreach ($gears as $gear)
                <div class="gear-row grid [grid-template-columns:2.5fr_1fr_1fr_1fr_1.2fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center" data-name="{{ strtolower($gear->name) }}"
                    >

                    {{-- Gear --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $gear->type === 'trap' ? 'target' : ($gear->type === 'ward' ? 'shield' : ($gear->type === 'anchor' ? 'anchor' : 'scan-line')) }}" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $gear->name }}
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ \Illuminate\Support\Str::limit($gear->description, 55) }}
                            </div>
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <span class="type-tag">{{ $gear->type }}</span>
                    </div>

                    {{-- Ecto --}}
                    <div class="text-[13.5px] text-[var(--text)] font-medium">
                        ₱ {{ $gear->ecto_cost }}
                    </div>

                    {{-- Energy --}}
                    <div class="text-[13.5px] text-[var(--text-secondary)]">
                        <i data-lucide="zap" style="width:14px; height:14px; color:#f59e0b; display:inline-block; vertical-align:middle; margin-right:2px;"></i> {{ $gear->energy_cost }}
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-1.5 justify-end">
                        @if (auth()->user()->isAdmin())
                            <a href="/gears/{{ $gear->id }}"
                               title="View details" class="w-[34px] h-[34px] rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] cursor-pointer flex items-center justify-center [transition:all_0.12s] hover:bg-[var(--bg)] hover:text-[var(--text)]"
                              
                              
                              >
                                <i data-lucide="eye" class="w-[15px] h-[15px]"></i>
                            </a>
                        @endif

                        <form method="POST" action="/gears/{{ $gear->id }}/buy" class="mt-0 mr-0 mb-0 ml-0">
                            @csrf
                            <button type="submit"
                                    title="Buy 1" class="w-[34px] h-[34px] rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] cursor-pointer flex items-center justify-center [transition:all_0.12s] hover:bg-[var(--bg)] hover:text-[var(--text)]"
                                   
                                   
                                   >
                                <i data-lucide="shopping-cart" class="w-[15px] h-[15px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- No search results message (JS toggles) --}}
        <div id="noResults" class="hidden [border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center mt-5">
            <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="search-x" class="w-5 h-5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[14px] font-semibold text-[var(--text)] mb-1">No matching gear</div>
            <div class="text-[13px] text-[var(--text-muted)]">Try a different search term.</div>
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-14 pr-6 pb-14 pl-6 text-center">
            <div class="w-[52px] h-[52px] mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="package-x" class="w-[22px] h-[22px] text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                No gear in catalog
            </div>
            <div class="text-[13px] text-[var(--text-muted)] mb-5">
                @if (auth()->user()->isAdmin())
                    Add the first gear item to get started.
                @else
                    Check back later — the catalog is being stocked.
                @endif
            </div>
            @if (auth()->user()->isAdmin())
                <a href="/gears/create" class="btn inline-flex">
                    <i data-lucide="plus"></i>
                    <span>Add Gear</span>
                </a>
            @endif
        </div>
    @endif

    {{-- ═══ CLIENT-SIDE SEARCH ═══════════════════════ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('gearSearch');
            const rows  = document.querySelectorAll('.gear-row');
            const empty = document.getElementById('noResults');

            if (!input) return;

            input.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                let visible = 0;

                rows.forEach(row => {
                    const match = row.dataset.name.includes(q);
                    row.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                if (empty) empty.style.display = (visible === 0 && q !== '') ? 'block' : 'none';
            });
        });
    </script>

@endsection