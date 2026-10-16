@extends('layouts.app')

@section('title', $gear->name . ' — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}

    <div class="max-w-[720px]">

        {{-- Main card --}}
        <div class="card pt-7 pr-7 pb-7 pl-7 mb-4">
            <div class="flex items-start gap-5 flex-wrap">

                {{-- Icon tile --}}
                <div class="w-16 h-16 rounded-[12px] bg-[var(--bg)] flex items-center justify-center shrink-0">
                    <i data-lucide="{{ $gear->type === 'trap' ? 'target' : ($gear->type === 'ward' ? 'shield' : ($gear->type === 'anchor' ? 'anchor' : 'scan-line')) }}" class="w-7 h-7 text-[var(--text)]"></i>
                </div>

                {{-- Info --}}
                <div class="grow min-w-0">
                    <span class="type-tag">{{ $gear->type }}</span>
                    <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[var(--text)] mt-2 mr-0 mb-1.5 ml-0">
                        {{ $gear->name }}
                    </h1>
                    <p class="text-[14px] text-[var(--text-secondary)] leading-[1.6]">
                        {{ $gear->description }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Cost details --}}
        <div class="grid [grid-template-columns:1fr_1fr] gap-4 mb-6">
            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                        Cost
                    </span>
                    <i data-lucide="gem" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[28px] font-semibold text-[var(--text)] tracking-[-0.02em] leading-[1.1]">
                    ₱ {{ $gear->ecto_cost }}
                </div>
                <div class="text-[12px] text-[var(--text-muted)] mt-1.5">
                    Per unit purchase price
                </div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                        Energy Cost
                    </span>
                    <i data-lucide="battery" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[28px] font-semibold text-[var(--text)] tracking-[-0.02em] leading-[1.1]">
                    <i data-lucide="zap" style="width:14px; height:14px; color:#f59e0b; display:inline-block; vertical-align:middle; margin-right:2px;"></i> {{ $gear->energy_cost }}
                </div>
                <div class="text-[12px] text-[var(--text-muted)] mt-1.5">
                    Consumed per hunt
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card pt-5 pr-5 pb-5 pl-5">
            <div class="flex gap-2.5 justify-between items-center flex-wrap">
                <div>
                    <div class="text-[14px] font-semibold text-[var(--text)] mb-0.5">
                        Want this in your inventory?
                    </div>
                    <div class="text-[12.5px] text-[var(--text-secondary)]">
                        Buy it from the shop for ₱ {{ $gear->ecto_cost }}.
                    </div>
                </div>

                <form method="POST" action="/gears/{{ $gear->id }}/buy" class="mt-0 mr-0 mb-0 ml-0">
                    @csrf
                    <button type="submit" class="btn">
                        <i data-lucide="shopping-cart"></i>
                        <span>Buy for ₱ {{ $gear->ecto_cost }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection