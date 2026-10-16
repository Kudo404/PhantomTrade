@extends('layouts.app')

@section('title', 'Spectral Gear Customizer — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[rgba(168,85,247,0.1)] text-[#9333ea] dark:text-[#c084fc] text-[11px] font-semibold uppercase tracking-[0.08em] mb-2">
                <i data-lucide="cpu" class="w-3.5 h-3.5"></i>
                Scenario 3: Ectoplasmic Energy & Spirit Ward Tech
            </div>
            <h1 class="text-[26px] font-bold tracking-[-0.03em] text-[var(--text)] mb-1">
                Spectral Gear Customizer & Telemetry
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Calibrate spirit wards, tune soul-anchors, configure containment frequencies, and monitor live life-force drain.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="/drain" class="btn bg-gradient-to-r from-[#9333ea] to-[#6366f1] text-white hover:opacity-95 shadow-md flex items-center gap-2">
                <i data-lucide="zap" class="w-4 h-4"></i>
                <span>Test in Drain Chamber</span>
            </a>
            <a href="/inventory" class="btn btn-secondary flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4"></i>
                <span>My Inventory</span>
            </a>
            @if ($equipped->count())
                <form action="/loadout/reset" method="POST" onsubmit="return confirm('Clear all equipped tactical slots?')">
                    @csrf
                    <button type="submit" class="btn btn-secondary text-[var(--danger)] hover:bg-[rgba(225,29,72,0.05)]" title="Unequip All">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ═══ SPECTRAL RESOURCE TRACKER HUD ════════════ --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-3">
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)] flex items-center gap-1.5">
                <i data-lucide="activity" class="w-3.5 h-3.5 text-[#9333ea]"></i>
                Biometric & Spectral Resource Telemetry
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
            @if ($telemetry['is_overloaded'])
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-[#fee2e2] text-[#dc2626] animate-pulse">
                    <i data-lucide="alert-triangle" class="w-3 h-3"></i> OVERLOAD HAZARD
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-[#ecfdf5] text-[#059669]">
                    <i data-lucide="shield-check" class="w-3 h-3"></i> TETHER STABLE
                </span>
            @endif
        </div>

        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(220px,_1fr))] gap-4">

            {{-- 1. Energy Drain Rate --}}
            <div class="p-5 border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Life-Force Drain Rate</span>
                    <i data-lucide="battery-charging" class="w-4 h-4 {{ $telemetry['total_drain'] > 35 ? 'text-[#e11d48]' : 'text-[#f59e0b]' }}"></i>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-[28px] font-bold tracking-[-0.02em] text-[var(--text)] leading-none">
                        {{ $telemetry['total_drain'] }}
                    </span>
                    <span class="text-[12px] font-medium text-[var(--text-muted)]">EP / hunt</span>
                </div>
                {{-- Progress Bar --}}
                <div class="w-full bg-[var(--border-subtle)] h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $telemetry['total_drain'] > 45 ? 'bg-[#e11d48]' : ($telemetry['total_drain'] > 25 ? 'bg-[#f59e0b]' : 'bg-[#10b981]') }}"
                         style="width: {{ min(100, ($telemetry['total_drain'] / 60) * 100) }}%"></div>
                </div>
                <div class="text-[11px] text-[var(--text-secondary)] mt-2 flex justify-between">
                    <span>Base: {{ $totalBaseEnergy }} EP</span>
                    <span>Mod: {{ $telemetry['total_drain'] - $totalBaseEnergy >= 0 ? '+' : '' }}{{ $telemetry['total_drain'] - $totalBaseEnergy }} EP</span>
                </div>
            </div>

            {{-- 2. Soul Tether Stability --}}
            <div class="p-5 border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Soul Tether Stability</span>
                    <i data-lucide="anchor" class="w-4 h-4 {{ $telemetry['stability'] < 50 ? 'text-[#e11d48]' : 'text-[#6366f1]' }}"></i>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-[28px] font-bold tracking-[-0.02em] text-[var(--text)] leading-none">
                        {{ $telemetry['stability'] }}%
                    </span>
                    <span class="text-[12px] font-medium {{ $telemetry['stability'] < 50 ? 'text-[#e11d48]' : 'text-[#059669]' }}">
                        {{ $telemetry['has_anchor'] ? 'Anchor Grounded' : 'NO ANCHOR!' }}
                    </span>
                </div>
                <div class="w-full bg-[var(--border-subtle)] h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $telemetry['stability'] < 50 ? 'bg-[#e11d48]' : ($telemetry['stability'] < 80 ? 'bg-[#f59e0b]' : 'bg-[#6366f1]') }}"
                         style="width: {{ $telemetry['stability'] }}%"></div>
                </div>
                <div class="text-[11px] text-[var(--text-secondary)] mt-2">
                    @if (! $telemetry['has_anchor'])
                        <span class="text-[#e11d48] font-medium">⚠️ Equip Soul-Anchor to prevent astral detachment.</span>
                    @else
                        <span>Grounding tether intact against spectral displacement.</span>
                    @endif
                </div>
            </div>

            {{-- 3. Spirit Ward Absorption --}}
            <div class="p-5 border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Spirit Ward Barrier</span>
                    <i data-lucide="shield" class="w-4 h-4 text-[#059669]"></i>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-[28px] font-bold tracking-[-0.02em] text-[var(--text)] leading-none">
                        {{ $telemetry['ward_absorption'] }}%
                    </span>
                    <span class="text-[12px] font-medium text-[var(--text-muted)]">Dissipation</span>
                </div>
                <div class="w-full bg-[var(--border-subtle)] h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 bg-[#059669]"
                         style="width: {{ $telemetry['ward_absorption'] }}%"></div>
                </div>
                <div class="text-[11px] text-[var(--text-secondary)] mt-2">
                    @if ($telemetry['has_ward'])
                        <span>Mitigates supernatural life-force corruption damage.</span>
                    @else
                        <span class="text-[var(--text-muted)]">No active spirit ward equipped.</span>
                    @endif
                </div>
            </div>

            {{-- 4. Rig Valuation & Capacity --}}
            <div class="p-5 border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Rig Value & Load</span>
                    <i data-lucide="cpu" class="w-4 h-4 text-[var(--text-muted)]"></i>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-[28px] font-bold tracking-[-0.02em] text-[var(--text)] leading-none">
                        {{ $equipped->count() }} <span class="text-[16px] font-normal text-[var(--text-muted)]">/ 4 Slots</span>
                    </span>
                    <span class="text-[13px] font-semibold text-[#059669]">
                        ₱ {{ $totalEctoValue }}
                    </span>
                </div>
                <div class="w-full bg-[var(--border-subtle)] h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-[var(--accent)]"
                         style="width: {{ ($equipped->count() / 4) * 100 }}%"></div>
                </div>
                <div class="text-[11px] text-[var(--text-secondary)] mt-2 flex justify-between">
                    <span>{{ 4 - $equipped->count() }} open slot(s)</span>
                    <span class="text-[var(--text-muted)]">Tactical Max</span>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══ TACTICAL SLOT DECK (SCENARIO 3 GEAR CATEGORIES) ═══ --}}
    <div class="flex items-center gap-3 mb-4">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Tactical Equipment Slots
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
        <span class="text-[12px] text-[var(--text-secondary)]">Click "Tune / Calibrate" to adjust operational profiles</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-9">

        @foreach ($slots as $slotKey => $slotData)
            @php $eq = $slotData['equipped']; @endphp

            <div class="border {{ $eq ? 'border-[var(--border)] bg-[var(--surface)]' : 'border-dashed border-[var(--border)] bg-[rgba(248,250,252,0.6)] dark:bg-[rgba(15,23,42,0.2)]' }} rounded-[var(--radius)] p-4 flex flex-col justify-between transition-all hover:shadow-md relative group">

                {{-- Card Header --}}
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $eq ? 'bg-[var(--accent)] text-white' : 'bg-[var(--border-subtle)] text-[var(--text-muted)]' }}">
                            <i data-lucide="{{ $slotData['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                                {{ $slotData['label'] }}
                            </div>
                            <div class="text-[10.5px] text-[var(--text-secondary)] leading-tight">
                                {{ $slotData['description'] }}
                            </div>
                        </div>
                    </div>

                    @if ($eq)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                            {{ $eq->tuning_mode === 'overclocked' ? 'bg-[#fee2e2] text-[#b91c1c]' :
                               ($eq->tuning_mode === 'shielded' ? 'bg-[#dbeafe] text-[#1e40af]' :
                               ($eq->tuning_mode === 'stealth' ? 'bg-[#ede9fe] text-[#6d28d9]' : 'bg-[var(--bg)] text-[var(--text-secondary)]')) }}">
                            {{ $eq->tuning_mode }}
                        </span>
                    @endif
                </div>

                {{-- Slot Body --}}
                @if ($eq)
                    <div class="my-2 p-3 rounded-lg bg-[var(--bg)] border border-[var(--border-subtle)]">
                        <div class="text-[14px] font-bold text-[var(--text)] truncate mb-0.5">
                            {{ $eq->display_name }}
                        </div>
                        @if ($eq->custom_name)
                            <div class="text-[11px] text-[var(--text-muted)] truncate mb-2">
                                Base: {{ $eq->gear->name }}
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-[var(--border-subtle)] text-[11.5px]">
                            <div>
                                <span class="text-[var(--text-muted)] block text-[10px] uppercase">Frequency</span>
                                <span class="font-semibold text-[var(--text)] flex items-center gap-1">
                                    <i data-lucide="radio" class="w-3 h-3 text-[#9333ea]"></i>
                                    {{ $eq->frequency_khz }} kHz
                                </span>
                            </div>
                            <div>
                                <span class="text-[var(--text-muted)] block text-[10px] uppercase">Energy Drain</span>
                                <span class="font-semibold text-[#f59e0b] flex items-center gap-1">
                                    <i data-lucide="zap" class="w-3 h-3"></i>
                                    {{ $eq->effective_energy_drain }} EP
                                </span>
                            </div>
                        </div>

                        <div class="mt-2 text-[10.5px] text-[var(--text-secondary)] truncate">
                            🎯 Targets: <span class="font-medium text-[var(--text)]">{{ $eq->target_entity }}</span>
                        </div>

                        @if ($eq->ward_reinforcement > 0)
                            <div class="mt-1 text-[10.5px] text-[#059669] font-medium">
                                🛡️ +{{ $eq->ward_reinforcement }}% Shield Reinforcement
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 mt-3 pt-2 border-t border-[var(--border-subtle)]">
                        <button type="button"
                                onclick="openTuningModal({{ json_encode([
                                    'gear_id'            => $eq->gear_id,
                                    'name'               => $eq->gear->name,
                                    'custom_name'        => $eq->custom_name,
                                    'tuning_mode'        => $eq->tuning_mode,
                                    'frequency_khz'      => $eq->frequency_khz,
                                    'ward_reinforcement' => $eq->ward_reinforcement,
                                    'slot'               => $slotKey,
                                    'base_energy'        => $eq->gear->energy_cost,
                                ]) }})"
                                class="btn btn-secondary flex-1 text-[12px] py-1.5 flex items-center justify-center gap-1.5">
                            <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                            <span>Tune / Mod</span>
                        </button>

                        <form action="/loadout/{{ $eq->gear_id }}/unequip" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary text-[var(--text-muted)] hover:text-[var(--danger)] py-1.5 px-2.5 text-[12px]" title="Unequip">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>

                @else
                    {{-- Empty Slot --}}
                    <div class="my-4 py-6 text-center">
                        <div class="w-10 h-10 rounded-full bg-[var(--border-subtle)] flex items-center justify-center mx-auto mb-2 text-[var(--text-muted)]">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </div>
                        <div class="text-[12.5px] font-medium text-[var(--text-muted)]">
                            Empty {{ $slotData['label'] }}
                        </div>
                        <div class="text-[11px] text-[var(--text-muted)] mt-1">
                            Equip an owned item below
                        </div>
                    </div>

                    <a href="#inventorySection" class="btn btn-secondary text-[12px] py-1.5 text-center block text-[var(--text-secondary)]">
                        Browse Gear
                    </a>
                @endif

            </div>
        @endforeach

    </div>

    {{-- ═══ AVAILABLE INVENTORY (WORKBENCH POOL) ════════ --}}
    <div id="inventorySection" class="flex items-center justify-between gap-3 mb-3.5 flex-wrap">
        <div class="flex items-center gap-3">
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Available Field Gear
            </span>
            <span class="text-[12px] text-[var(--text-muted)]">{{ $myItems->count() }} owned item(s)</span>
        </div>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
        <div class="flex gap-1.5 text-[12px]">
            <span class="text-[var(--text-muted)]">Slot Compatibility:</span>
            <span class="font-medium text-[var(--text)]">Wards • Anchors • Traps • Scanners</span>
        </div>
    </div>

    @if ($myItems->count())
        <div class="border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden shadow-sm">
            <div class="grid [grid-template-columns:2.5fr_1.2fr_0.8fr_1fr_1fr_1.4fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] border-b border-[var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Gear Specimen</div>
                <div>Tactical Slot</div>
                <div>Owned</div>
                <div>Ecto Cost</div>
                <div>Base Drain</div>
                <div class="text-right">Action</div>
            </div>

            @foreach ($myItems as $item)
                @php
                    $isEquipped = in_array($item->gear->id, $equippedGearIds);
                    $gearType = $item->gear->type;
                    $slotLabel = match($gearType) {
                        'ward'    => 'Spirit Ward',
                        'anchor'  => 'Soul-Anchor',
                        'trap'    => 'Containment',
                        'scanner' => 'Spectral Scanner',
                        default   => 'Auxiliary Gadget'
                    };
                @endphp

                <div class="grid [grid-template-columns:2.5fr_1.2fr_0.8fr_1fr_1fr_1.4fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 border-b border-[var(--border-subtle)] items-center {{ $isEquipped ? 'bg-[rgba(15,23,42,0.02)]' : '' }}">

                    {{-- Specimen --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $isEquipped ? 'bg-[var(--accent)] text-white' : 'bg-[var(--bg)] text-[var(--text-secondary)]' }}">
                            <i data-lucide="{{ $gearType === 'ward' ? 'shield' : ($gearType === 'anchor' ? 'anchor' : ($gearType === 'trap' ? 'target' : 'scan-line')) }}" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium text-[var(--text)] truncate">
                                {{ $item->gear->name }}
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)] truncate">
                                {{ \Illuminate\Support\Str::limit($item->gear->description, 55) }}
                            </div>
                        </div>
                    </div>

                    {{-- Tactical Slot --}}
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-[var(--bg)] border border-[var(--border-subtle)] text-[var(--text)]">
                            {{ $slotLabel }}
                        </span>
                    </div>

                    {{-- Owned --}}
                    <div class="text-[13px] text-[var(--text-secondary)]">
                        {{ $item->quantity }}×
                    </div>

                    {{-- Cost --}}
                    <div class="text-[13px] font-semibold text-[#059669]">
                        ₱ {{ $item->gear->ecto_cost }}
                    </div>

                    {{-- Energy --}}
                    <div class="text-[13px] font-medium text-[#f59e0b] flex items-center gap-1">
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                        {{ $item->gear->energy_cost }} EP
                    </div>

                    {{-- Action --}}
                    <div class="text-right">
                        @if ($isEquipped)
                            <div class="inline-flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-[12px] font-semibold text-[#059669]">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Slotted
                                </span>
                                <form action="/loadout/{{ $item->gear->id }}/unequip" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary text-[11.5px] py-1 px-2.5">
                                        Unequip
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="/loadout/{{ $item->gear->id }}/equip" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary text-[12px] py-1 px-3 hover:border-[var(--accent)] hover:text-[var(--text)]">
                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                                    <span>Equip to {{ explode(' ', $slotLabel)[0] }}</span>
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] p-12 text-center">
            <div class="w-12 h-12 rounded-full bg-[var(--border-subtle)] flex items-center justify-center mx-auto mb-3 text-[var(--text-muted)]">
                <i data-lucide="package-open" class="w-6 h-6"></i>
            </div>
            <h3 class="text-[15px] font-semibold text-[var(--text)] mb-1">Your Armory is Empty</h3>
            <p class="text-[13px] text-[var(--text-secondary)] max-w-[420px] mx-auto mb-5">
                Acquire spirit wards, soul-anchors, and ghost traps from the Gear Shop or peer-to-peer Marketplace to build your spectral hunting rig.
            </p>
            <div class="flex justify-center gap-3">
                <a href="/gears" class="btn">Browse Gear Shop</a>
                <a href="/marketplace" class="btn btn-secondary">Explore Marketplace</a>
            </div>
        </div>
    @endif


    {{-- ═══ TUNING WORKBENCH MODAL ════════════════════ --}}
    <div id="tuningModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-[var(--surface)] border border-[var(--border)] rounded-xl max-w-[520px] w-full p-6 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">

            {{-- Close Button --}}
            <button type="button" onclick="closeTuningModal()" class="absolute top-4 right-4 text-[var(--text-muted)] hover:text-[var(--text)]">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            {{-- Modal Header --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-lg bg-[rgba(147,51,234,0.1)] text-[#9333ea] flex items-center justify-center shrink-0">
                    <i data-lucide="sliders" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-[16px] font-bold text-[var(--text)]" id="modalGearTitle">
                        Calibrate Gear
                    </h3>
                    <p class="text-[12px] text-[var(--text-secondary)]">
                        Adjust operating mode, spectral frequency, and reinforcement.
                    </p>
                </div>
            </div>

            {{-- Tuning Form --}}
            <form id="tuningForm" method="POST" action="">
                @csrf

                {{-- Custom Codename --}}
                <div class="mb-4">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">
                        Hunter Codename / Rig Label
                    </label>
                    <input type="text"
                           name="custom_name"
                           id="tuneCustomName"
                           placeholder="e.g. Aegis Spirit Ward Mk II"
                           maxlength="80"
                           class="w-full px-3 py-2 rounded-lg border border-[var(--border)] bg-[var(--bg)] text-[13px] text-[var(--text)] focus:outline-none focus:border-[var(--accent)]">
                </div>

                {{-- Operating Profile (Tuning Mode) --}}
                <div class="mb-5">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">
                        Resonance Profile
                    </label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="border border-[var(--border)] rounded-lg p-3 cursor-pointer hover:border-[var(--accent)] transition-colors flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[12.5px] font-bold text-[var(--text)]">⚖️ Balanced</span>
                                <input type="radio" name="tuning_mode" value="balanced" id="modeBalanced" class="accent-[#9333ea]">
                            </div>
                            <span class="text-[11px] text-[var(--text-secondary)]">1.0× Drain • Standard stability</span>
                        </label>

                        <label class="border border-[var(--border)] rounded-lg p-3 cursor-pointer hover:border-[#dc2626] transition-colors flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[12.5px] font-bold text-[#dc2626]">⚡ Overclocked</span>
                                <input type="radio" name="tuning_mode" value="overclocked" id="modeOverclocked" class="accent-[#dc2626]">
                            </div>
                            <span class="text-[11px] text-[var(--text-secondary)]">+40% Power • +50% Drain</span>
                        </label>

                        <label class="border border-[var(--border)] rounded-lg p-3 cursor-pointer hover:border-[#2563eb] transition-colors flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[12.5px] font-bold text-[#2563eb]">🛡️ Ward-Hardened</span>
                                <input type="radio" name="tuning_mode" value="shielded" id="modeShielded" class="accent-[#2563eb]">
                            </div>
                            <span class="text-[11px] text-[var(--text-secondary)]">+35% Shield • -20% Drain</span>
                        </label>

                        <label class="border border-[var(--border)] rounded-lg p-3 cursor-pointer hover:border-[#7c3aed] transition-colors flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[12.5px] font-bold text-[#7c3aed]">🔇 Stealth-Dampened</span>
                                <input type="radio" name="tuning_mode" value="stealth" id="modeStealth" class="accent-[#7c3aed]">
                            </div>
                            <span class="text-[11px] text-[var(--text-secondary)]">-30% Drain • Low aggro</span>
                        </label>
                    </div>
                </div>

                {{-- Frequency Tuning Slider --}}
                <div class="mb-5 p-3.5 rounded-lg bg-[var(--bg)] border border-[var(--border-subtle)]">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            Spectral Frequency Resonance
                        </label>
                        <span class="text-[13px] font-bold text-[#9333ea]" id="freqDisplay">
                            440 kHz
                        </span>
                    </div>

                    <input type="range"
                           name="frequency_khz"
                           id="tuneFreqSlider"
                           min="100"
                           max="1000"
                           step="10"
                           value="440"
                           class="w-full accent-[#9333ea] cursor-pointer"
                           oninput="updateFrequencyDisplay(this.value)">

                    <div class="flex justify-between text-[10px] text-[var(--text-muted)] mt-1">
                        <span>100 kHz (Kinetic)</span>
                        <span>500 kHz (Acoustic)</span>
                        <span>1000 kHz (Astral)</span>
                    </div>

                    <div class="text-[11.5px] font-medium text-[var(--text)] mt-2 pt-2 border-t border-[var(--border-subtle)] flex items-center justify-between">
                        <span class="text-[var(--text-secondary)]">Entity Calibration:</span>
                        <span class="text-[#9333ea] font-semibold" id="entityTargetDisplay">
                            Banshee (Acoustic Resonance)
                        </span>
                    </div>
                </div>

                {{-- Ward Reinforcement Slider (Optional for Wards) --}}
                <div id="wardBoostContainer" class="mb-5">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            Ward Shield Reinforcement
                        </label>
                        <span class="text-[12px] font-bold text-[#059669]" id="wardBoostDisplay">+0%</span>
                    </div>
                    <input type="range"
                           name="ward_reinforcement"
                           id="tuneWardBoost"
                           min="0"
                           max="50"
                           step="5"
                           value="0"
                           class="w-full accent-[#059669] cursor-pointer"
                           oninput="document.getElementById('wardBoostDisplay').textContent = '+' + this.value + '%'">
                    <p class="text-[10.5px] text-[var(--text-secondary)] mt-1">
                        Adds direct damage dissipation against spectral corruption (+1 EP drain per 10% boost).
                    </p>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex justify-end gap-2.5 pt-2 border-t border-[var(--border-subtle)]">
                    <button type="button" onclick="closeTuningModal()" class="btn btn-secondary text-[13px]">
                        Cancel
                    </button>
                    <button type="submit" class="btn text-[13px] bg-[#9333ea] text-white hover:bg-[#7e22ce]">
                        Save Custom Calibration
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript for Tuning Workbench Modal --}}
    <script>
        function openTuningModal(data) {
            const form = document.getElementById('tuningForm');
            form.action = '/loadout/' + data.gear_id + '/tune';

            document.getElementById('modalGearTitle').textContent = 'Calibrate ' + data.name;
            document.getElementById('tuneCustomName').value = data.custom_name || '';

            // Set radio mode
            const mode = data.tuning_mode || 'balanced';
            const radio = document.querySelector(`input[name="tuning_mode"][value="${mode}"]`);
            if (radio) radio.checked = true;

            // Set frequency
            const freq = data.frequency_khz || 440;
            document.getElementById('tuneFreqSlider').value = freq;
            updateFrequencyDisplay(freq);

            // Ward reinforcement
            const wardBoost = data.ward_reinforcement || 0;
            document.getElementById('tuneWardBoost').value = wardBoost;
            document.getElementById('wardBoostDisplay').textContent = '+' + wardBoost + '%';

            // Show modal
            document.getElementById('tuningModal').classList.remove('hidden');
        }

        function closeTuningModal() {
            document.getElementById('tuningModal').classList.add('hidden');
        }

        function updateFrequencyDisplay(val) {
            document.getElementById('freqDisplay').textContent = val + ' kHz';

            let entity = 'Poltergeist (Kinetic Disruption)';
            if (val >= 350 && val < 550) {
                entity = 'Banshee (Acoustic Resonance)';
            } else if (val >= 550 && val < 750) {
                entity = 'Shadow Fiend (Astral Vapor)';
            } else if (val >= 750) {
                entity = 'Ancient Wraith (Ethereal Static)';
            }

            document.getElementById('entityTargetDisplay').textContent = entity;
        }

        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeTuningModal();
        });
    </script>

@endsection