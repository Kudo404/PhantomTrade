@extends('layouts.app')

@section('title', 'My Trade Proposals — Phantom Trade')

@section('content')

    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                My Trade Proposals
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Every trade you've proposed on the marketplace.
            </p>
        </div>

        <a href="/marketplace" class="btn btn-secondary">
            <i data-lucide="store"></i>
            <span>Browse Marketplace</span>
        </a>
    </div>

    {{-- ═══ SUMMARY STRIP ═══ --}}
    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(160px,_1fr))] gap-0 [border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden mb-8">
        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total</span>
                <i data-lucide="send" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $total }}</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Pending</span>
                <i data-lucide="clock" class="w-3.5 h-3.5 text-[var(--warning)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $pending }}</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Accepted</span>
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[var(--success)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $accepted }}</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Rejected</span>
                <i data-lucide="x-circle" class="w-3.5 h-3.5 text-[var(--danger)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $rejected }}</div>
        </div>
    </div>

    {{-- ═══ PROPOSALS LIST ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            All Proposals
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($proposals->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">

            <div class="grid [grid-template-columns:2fr_2fr_1.3fr_1.2fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>You Offered</div>
                <div>For</div>
                <div>Sent</div>
                <div class="text-right">Status</div>
            </div>

            @foreach ($proposals as $p)
                <div class="grid [grid-template-columns:2fr_2fr_1.3fr_1.2fr] gap-4 pt-4 pr-5 pb-4 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-[15px] h-[15px] text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $p->offered_quantity }}× {{ $p->offeredGear->name }}
                            </div>
                            @if ($p->note)
                                <div class="text-[11px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                    "{{ \Illuminate\Support\Str::limit($p->note, 40) }}"
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="min-w-0">
                        <div class="text-[13px] text-[var(--text)] font-medium whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ $p->listing->gear->name }}
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <div class="text-[11px] text-[var(--text-muted)]">
                                from <x-user-name :user="$p->listing->merchant" />
                            </div>
                            <a href="/messages/start/{{ $p->listing->merchant_id }}" title="Message Merchant" class="text-[var(--text-muted)] no-underline inline-flex items-center hover:text-[var(--text)]"
                              
                              
                              >
                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>

                    <div class="text-[12.5px] text-[var(--text-secondary)]">
                        {{ $p->created_at->diffForHumans() }}
                    </div>

                    <div class="text-right">
                        @if ($p->status === 'pending')
                            <span class="inline-flex items-center gap-[5px] text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--warning)]">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                Pending
                            </span>
                        @elseif ($p->status === 'accepted')
                            <span class="inline-flex items-center gap-[5px] text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--success)]">
                                <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                Accepted
                            </span>
                        @else
                            <span class="inline-flex items-center gap-[5px] text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--danger)]">
                                <i data-lucide="x-circle" class="w-3 h-3"></i>
                                Rejected
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-14 pr-6 pb-14 pl-6 text-center">
            <div class="w-[52px] h-[52px] mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="arrow-left-right" class="w-[22px] h-[22px] text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                No trade proposals yet
            </div>
            <div class="text-[13px] text-[var(--text-muted)] mb-5">
                Browse the marketplace and click <strong>Trade</strong> on any listing to propose a swap.
            </div>
            <a href="/marketplace" class="btn inline-flex">
                <i data-lucide="store"></i>
                <span>Browse Marketplace</span>
            </a>
        </div>
    @endif

@endsection