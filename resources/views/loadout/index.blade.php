@extends('layouts.app')

@section('title', 'My Loadout — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                My Loadout
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Equip gear for your next hunt. One of each item at a time.
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            <a href="/inventory" class="btn btn-secondary">
                <i data-lucide="package"></i>
                <span>My Inventory</span>
            </a>
        </div>
    </div>

    {{-- ═══ TOTALS STRIP ═════════════════════════════ --}}
    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-0 [border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden mb-8">
        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Equipped</span>
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                {{ $equipped->count() }}
            </div>
            <div class="text-[12px] text-[var(--text-muted)] mt-1.5">Items equipped</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Value</span>
                <i data-lucide="wallet" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                ₱ {{ $totalEcto }}
            </div>
            <div class="text-[12px] text-[var(--text-muted)] mt-1.5">Combined cost</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Energy Cost</span>
                <i data-lucide="battery" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                <i data-lucide="zap" style="width:14px; height:14px; color:#f59e0b; display:inline-block; vertical-align:middle; margin-right:2px;"></i> {{ $totalEnergy }}
            </div>
            <div class="text-[12px] text-[var(--text-muted)] mt-1.5">Per hunt</div>
        </div>
    </div>

    {{-- ═══ YOUR GEAR ════════════════════════════════ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Your Gear
        </span>
        <span class="text-[12px] text-[var(--text-muted)]">{{ $myItems->count() }} item(s)</span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($myItems->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">

            {{-- Header row --}}
            <div class="grid [grid-template-columns:2.5fr_1fr_0.7fr_1fr_1fr_1.2fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Gear</div>
                <div>Type</div>
                <div>Owned</div>
                <div>Cost</div>
                <div>Energy</div>
                <div class="text-right">Status</div>
            </div>

            @foreach ($myItems as $item)
                @php $isEquipped = in_array($item->gear->id, $equippedIds); @endphp
                <div class="grid [grid-template-columns:2.5fr_1fr_0.7fr_1fr_1fr_1.2fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center {{ $isEquipped ? 'bg-[rgba(15,23,42,0.02)]' : '' }}">

                    {{-- Gear --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg {{ $isEquipped ? 'bg-[var(--accent)]' : 'bg-[var(--bg)]' }} flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $item->gear->type === 'trap' ? 'target' : ($item->gear->type === 'ward' ? 'shield' : ($item->gear->type === 'anchor' ? 'anchor' : 'scan-line')) }}" class="w-4 h-4 {{ $isEquipped ? 'text-[#fff]' : 'text-[var(--text-secondary)]' }}"></i>
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

                    {{-- Owned --}}
                    <div class="text-[13px] text-[var(--text)] font-medium">
                        ×{{ $item->quantity }}
                    </div>

                    {{-- Ecto --}}
                    <div class="text-[13.5px] text-[var(--text)] font-medium">
                        ₱ {{ $item->gear->ecto_cost }}
                    </div>

                    {{-- Energy --}}
                    <div class="text-[13.5px] text-[var(--text-secondary)]">
                        <i data-lucide="zap" style="width:14px; height:14px; color:#f59e0b; display:inline-block; vertical-align:middle; margin-right:2px;"></i> {{ $item->gear->energy_cost }}
                    </div>

                    {{-- Status / action --}}
                    <div class="flex justify-end">
                        @if ($isEquipped)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--success)]">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Equipped
                                </span>
                                <form method="POST" action="/loadout/{{ $item->gear->id }}/unequip" class="mt-0 mr-0 mb-0 ml-0">
                                    @csrf
                                    <button type="submit" title="Unequip"
                                           
                                           
                                            class="w-[34px] h-[34px] rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] cursor-pointer flex items-center justify-center [transition:all_0.12s]  hover:bg-[#fef2f2] hover:text-[var(--danger)] hover:[border-color:#fecaca]">
                                        <i data-lucide="x" class="w-[15px] h-[15px]"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="/loadout/{{ $item->gear->id }}/equip" class="mt-0 mr-0 mb-0 ml-0">
                                @csrf
                                <button type="submit" class="btn btn-secondary text-[12.5px] pt-1.5 pr-3 pb-1.5 pl-3">
                                    <i data-lucide="plus"></i>
                                    <span>Equip</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-14 pr-6 pb-14 pl-6 text-center">
            <div class="w-[52px] h-[52px] mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="backpack" class="w-[22px] h-[22px] text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                No gear to equip
            </div>
            <div class="text-[13px] text-[var(--text-muted)] mb-5">
                Purchase gear from the shop first.
            </div>
            <a href="/gears" class="btn inline-flex">
                <i data-lucide="store"></i>
                <span>Browse Gear Shop</span>
            </a>
        </div>
    @endif

@endsection