@extends('layouts.app')

@section('title', 'My Wanted Ads — Phantom Trade')

@section('content')

    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                My Wanted Ads
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Ads you've posted and responses from other users.
            </p>
        </div>

        <a href="/marketplace/wanted/create" class="btn">
            <i data-lucide="plus"></i>
            <span>New Wanted Ad</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="error-banner">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ═══ SUMMARY ═══ --}}
    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(160px,_1fr))] gap-0 [border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden mb-8">
        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Ads</span>
                <i data-lucide="tag" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $totalPosts }}</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Open</span>
                <i data-lucide="circle" class="w-3.5 h-3.5 text-[var(--success)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $openPosts }}</div>
        </div>

        <div class="pt-5 pr-6 pb-5 pl-6">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Pending Responses</span>
                <i data-lucide="clock" class="w-3.5 h-3.5 text-[var(--warning)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $pendingCount }}</div>
        </div>
    </div>

    {{-- ═══ ADS LIST ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            All Wanted Ads
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($myPosts->count())
        @foreach ($myPosts as $post)
            <div class="card pt-0 pr-0 pb-0 pl-0 mb-3.5 overflow-hidden">

                <div class="flex justify-between items-center pt-[18px] pr-[22px] pb-[18px] pl-[22px] [border-bottom:1px_solid_var(--border-subtle)]">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="tag" class="w-[18px] h-[18px] text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[15px] font-semibold text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $post->gear->name }}
                            </div>
                            <div class="text-[12.5px] text-[var(--text-secondary)] mt-0.5">
                                Looking for <strong class="text-[var(--text)]">×{{ $post->quantity }}</strong>
                                · offering ₱ {{ $post->price_per_unit }} each
                                · {{ $post->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0">
                        @if ($post->status === 'open')
                            <span class="inline-flex items-center gap-[5px] text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--success)]">
                                <i data-lucide="circle" class="w-2.5 h-2.5"></i>
                                Open
                            </span>
                        @elseif ($post->status === 'fulfilled')
                            <span class="inline-flex items-center gap-[5px] text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--success)]">
                                <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                Fulfilled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-[5px] text-[11px] font-semibold uppercase tracking-[0.05em] text-[var(--danger)]">
                                <i data-lucide="x-circle" class="w-3 h-3"></i>
                                Cancelled
                            </span>
                        @endif
                    </div>
                </div>

                @if ($post->responses->count())
                    <div class="pt-[18px] pr-[22px] pb-[18px] pl-[22px]">
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.06em] mb-3">
                            Responses ({{ $post->responses->count() }})
                        </div>

                        @foreach ($post->responses as $r)
                            <div class="grid [grid-template-columns:2fr_1fr_1.2fr_1.3fr] gap-4 pt-3.5 pr-4 pb-3.5 pl-4 bg-[var(--bg)] rounded-[var(--radius-sm)] mb-2 items-center">

                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[11px] shrink-0">
                                        {{ strtoupper(substr($r->responder->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                                <x-user-name :user="$r->responder" />
                                            </div>
                                            <a href="/messages/start/{{ $r->responder_id }}" title="Message Responder" class="text-[var(--text-muted)] no-underline inline-flex items-center hover:text-[var(--text)]"
                                              
                                              
                                              >
                                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                            </a>
                                        </div>
                                        <div class="text-[11px] text-[var(--text-muted)]">{{ $r->responder->roleLabel() }}</div>
                                    </div>
                                </div>

                                <div class="text-[13px] text-[var(--text)] font-medium">
                                    {{ $r->offered_quantity }}×
                                </div>

                                <div class="text-[13px] text-[var(--text)] font-medium">
                                    ₱ {{ $r->price_per_unit }} each
                                </div>

                                <div class="flex gap-1.5 justify-end">
                                    @if ($r->isPending() && $post->isOpen())
                                        <form method="POST" action="/marketplace/wanted/responses/{{ $r->id }}/accept" class="mt-0 mr-0 mb-0 ml-0">
                                            @csrf
                                            <button type="submit" title="Accept"
                                                   
                                                   
                                                    class="w-8 h-8 rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--success)] cursor-pointer flex items-center justify-center  hover:bg-[#ecfdf5] hover:[border-color:#a7f3d0]">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="/marketplace/wanted/responses/{{ $r->id }}/reject" class="mt-0 mr-0 mb-0 ml-0">
                                            @csrf
                                            <button type="submit" title="Reject"
                                                   
                                                   
                                                    class="w-8 h-8 rounded-[var(--radius-sm)] [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--danger)] cursor-pointer flex items-center justify-center  hover:bg-[#fef2f2] hover:[border-color:#fecaca]">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @elseif ($r->status === 'accepted')
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
                    <div class="pt-5 pr-[22px] pb-5 pl-[22px] text-center">
                        <div class="inline-flex items-center gap-2 text-[12.5px] text-[var(--text-muted)]">
                            <i data-lucide="inbox" class="w-3.5 h-3.5"></i>
                            No responses yet — owners of this gear have been notified.
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-14 pr-6 pb-14 pl-6 text-center">
            <div class="w-[52px] h-[52px] mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="tag" class="w-[22px] h-[22px] text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                No wanted ads yet
            </div>
            <div class="text-[13px] text-[var(--text-muted)] mb-5">
                Post an ad for anything you're looking for.
            </div>
            <a href="/marketplace/wanted/create" class="btn inline-flex">
                <i data-lucide="plus"></i>
                <span>Post Wanted Ad</span>
            </a>
        </div>
    @endif

@endsection