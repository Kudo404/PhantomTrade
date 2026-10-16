@extends('layouts.app')

@section('title', 'Trade Proposals — Phantom Trade')

@section('content')

    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Trade Proposals
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Offers from buyers wanting to trade for your listings.
            </p>
        </div>

        <a href="/marketplace/mine" class="btn btn-secondary">
            <i data-lucide="arrow-left"></i>
            <span>My Listings</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="error-banner">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ═══ PENDING ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <i data-lucide="clock" class="w-3.5 h-3.5 text-[var(--warning)]"></i>
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Pending
        </span>
        <span class="text-[12px] text-[var(--text-muted)]">{{ $pending->count() }}</span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($pending->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden mb-8">

            <div class="grid [grid-template-columns:2fr_2fr_1fr_1fr_1.3fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Buyer</div>
                <div>They Offer</div>
                <div>For Yours</div>
                <div>Value</div>
                <div class="text-right">Decision</div>
            </div>

            @foreach ($pending as $p)
                <div class="grid [grid-template-columns:2fr_2fr_1fr_1fr_1.3fr] gap-4 pt-4 pr-5 pb-4 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    {{-- Buyer --}}
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[12px] shrink-0">
                            {{ strtoupper(substr($p->buyer->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                <x-user-name :user="$p->buyer" />
                            </div>
                            <div class="text-[11px] text-[var(--text-muted)]">
                                {{ $p->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    {{-- Offered gear --}}
                    <div class="min-w-0">
                        <div class="text-[13px] text-[var(--text)] font-medium whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ $p->offered_quantity }}× {{ $p->offeredGear->name }}
                        </div>
                        @if ($p->note)
                            <div class="text-[11px] text-[var(--text-muted)] mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis">
                                "{{ \Illuminate\Support\Str::limit($p->note, 45) }}"
                            </div>
                        @endif
                    </div>

                    {{-- Listing --}}
                    <div class="text-[13px] text-[var(--text-secondary)] whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ $p->listing->gear->name }}
                    </div>

                    {{-- Value --}}
                    <div class="text-[13px] text-[var(--text)] font-medium">
                        ₱ {{ $p->offeredValue() }}
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-1.5 justify-end">
                        <a href="/messages/start/{{ $p->buyer_id }}" title="Message Buyer" class="w-[34px] h-[34px] rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] flex items-center justify-center no-underline hover:bg-[var(--bg)] hover:text-[var(--text)]"
                          
                          
                          >
                            <i data-lucide="message-circle" class="w-[15px] h-[15px]"></i>
                        </a>
                        <form method="POST" action="/marketplace/proposals/{{ $p->id }}/accept" class="mt-0 mr-0 mb-0 ml-0">
                            @csrf
                            <button type="submit" title="Accept"
                                   
                                   
                                    class="w-[34px] h-[34px] rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--success)] cursor-pointer flex items-center justify-center  hover:bg-[#ecfdf5] hover:[border-color:#a7f3d0]">
                                <i data-lucide="check" class="w-[15px] h-[15px]"></i>
                            </button>
                        </form>
                        <form method="POST" action="/marketplace/proposals/{{ $p->id }}/reject" class="mt-0 mr-0 mb-0 ml-0">
                            @csrf
                            <button type="submit" title="Reject"
                                   
                                   
                                    class="w-[34px] h-[34px] rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--danger)] cursor-pointer flex items-center justify-center  hover:bg-[#fef2f2] hover:[border-color:#fecaca]">
                                <i data-lucide="x" class="w-[15px] h-[15px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center mb-8">
            <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-[var(--success)]"></i>
            </div>
            <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                All caught up
            </div>
            <div class="text-[13px] text-[var(--text-muted)]">
                No pending proposals right now.
            </div>
        </div>
    @endif

    {{-- ═══ RECENTLY RESPONDED ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <i data-lucide="history" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Recently Responded
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($recent->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">

            <div class="grid [grid-template-columns:2fr_2fr_1fr_1fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Buyer</div>
                <div>Offered</div>
                <div>For Yours</div>
                <div class="text-right">Status</div>
            </div>

            @foreach ($recent as $p)
                <div class="grid [grid-template-columns:2fr_2fr_1fr_1fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-[var(--bg)] [border:1px_solid_var(--border)] text-[var(--text-secondary)] flex items-center justify-center font-semibold text-[11px] shrink-0">
                            {{ strtoupper(substr($p->buyer->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                <x-user-name :user="$p->buyer" />
                            </div>
                            <div class="text-[11px] text-[var(--text-muted)]">
                                {{ $p->responded_at?->diffForHumans() ?? $p->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div class="text-[13px] text-[var(--text-secondary)] whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ $p->offered_quantity }}× {{ $p->offeredGear->name }}
                    </div>

                    <div class="text-[13px] text-[var(--text-secondary)] whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ $p->listing->gear->name }}
                    </div>

                    <div class="text-right">
                        @if ($p->status === 'accepted')
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
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-10 pr-6 pb-10 pl-6 text-center">
            <div class="text-[13px] text-[var(--text-muted)]">No responses yet.</div>
        </div>
    @endif

@endsection