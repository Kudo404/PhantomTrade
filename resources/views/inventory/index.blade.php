@extends('layouts.app')

@section('title', 'My Inventory — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                My Inventory
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Everything you currently own.
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            @if (auth()->user()->isGhostHunter())
                <a href="/loadout" class="btn btn-secondary">
                    <i data-lucide="backpack"></i>
                    <span>Manage Loadout</span>
                </a>
            @endif
            @if (auth()->user()->isMerchant())
                <a href="/marketplace/create" class="btn">
                    <i data-lucide="plus"></i>
                    <span>Create Listing</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ═══ SUMMARY STRIP ════════════════════════════ --}}
    @php
        $summary = [
            ['label' => 'Total Items',     'value' => $totalItems,   'icon' => 'package'],
            ['label' => 'Total Value','value' => $totalEctoValue, 'icon' => 'wallet'],
            ['label' => 'Total Energy',    'value' => $totalEnergyCost, 'icon' => 'battery'],
            ['label' => 'Unique Gear',     'value' => auth()->user()->inventories()->count(), 'icon' => 'layers'],
        ];
    @endphp

    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-8">
        @foreach ($summary as $s)
            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                        {{ $s['label'] }}
                    </span>
                    <i data-lucide="{{ $s['icon'] }}" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                    {{ $s['value'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ INVENTORY TABLE ══════════════════════════ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Items Owned
        </span>
        <span class="text-[12px] text-[var(--text-muted)]">{{ $items->count() }}</span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($items->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">

            {{-- Header row --}}
            <div class="grid [grid-template-columns:2.5fr_1fr_0.7fr_1fr_1fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Gear</div>
                <div>Type</div>
                <div>Qty</div>
                <div>Cost / Unit</div>
                <div>Energy / Unit</div>
            </div>

            @foreach ($items as $item)
                <div class="grid [grid-template-columns:2.5fr_1fr_0.7fr_1fr_1fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    {{-- Gear --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $item->gear->name }}
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ \Illuminate\Support\Str::limit($item->gear->description, 50) }}
                            </div>
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <span class="type-tag">{{ $item->gear->type }}</span>
                    </div>

                    {{-- Qty --}}
                    <div class="text-[13px] text-[var(--text)] font-medium">
                        ×{{ $item->quantity }}
                    </div>

                    {{-- Ecto cost --}}
                    <div class="text-[13.5px] text-[var(--text)] font-medium">
                        ₱ {{ $item->gear->ecto_cost }}
                    </div>

                    {{-- Energy cost --}}
                    <div class="text-[13.5px] text-[var(--text-secondary)]">
                        <i data-lucide="zap" style="width:14px; height:14px; color:#f59e0b; display:inline-block; vertical-align:middle; margin-right:2px;"></i> {{ $item->gear->energy_cost }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-14 pr-6 pb-14 pl-6 text-center">
            <div class="w-[52px] h-[52px] mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="package" class="w-[22px] h-[22px] text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                Your inventory is empty
            </div>
            <div class="text-[13px] text-[var(--text-muted)] mb-5">
                Purchase gear from the shop to get started.
            </div>
            <a href="/gears" class="btn inline-flex">
                <i data-lucide="store"></i>
                <span>Browse Gear Shop</span>
            </a>
        </div>
    @endif

@endsection