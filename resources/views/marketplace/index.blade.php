@extends('layouts.app')

@section('title', 'Marketplace — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Marketplace
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Buy, sell, or trade gear with other hunters and merchants.
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">
            <a href="/marketplace/wanted/create" class="btn btn-secondary">
                <i data-lucide="tag"></i>
                <span>Post Wanted Ad</span>
            </a>

            @if (auth()->user()->isMerchant())
                <a href="/marketplace/mine" class="btn btn-secondary">
                    <i data-lucide="list"></i>
                    <span>My Listings</span>
                </a>
                <a href="/marketplace/create" class="btn">
                    <i data-lucide="plus"></i>
                    <span>Create Listing</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ═══ ACTIVE LISTINGS ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Active Listings
        </span>
        <span class="text-[12px] text-[var(--text-muted)]">{{ $listings->count() }}</span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @if ($listings->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden mb-8">

            {{-- Header row --}}
            <div class="grid [grid-template-columns:2.5fr_1.2fr_0.7fr_1fr_1.5fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Gear</div>
                <div>Seller</div>
                <div>Qty</div>
                <div>Price / Unit</div>
                <div class="text-right">Actions</div>
            </div>

            @foreach ($listings as $listing)
                <div class="grid [grid-template-columns:2.5fr_1.2fr_0.7fr_1fr_1.5fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    {{-- Gear --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $listing->gear->name }}
                            </div>
                            <div class="text-[11px] text-[var(--text-muted)] uppercase tracking-[0.05em]">
                                {{ $listing->gear->type }}
                            </div>
                        </div>
                    </div>

                    {{-- Seller --}}
                    <div class="text-[13px] text-[var(--text-secondary)] whitespace-nowrap overflow-hidden text-ellipsis">
                        <x-user-name :user="$listing->merchant" />
                    </div>

                    {{-- Qty --}}
                    <div class="text-[13px] text-[var(--text)] font-medium">
                        ×{{ $listing->quantity }}
                    </div>

                    {{-- Price --}}
                    <div class="text-[13.5px] text-[var(--text)] font-semibold">
                        ₱ {{ $listing->price_per_unit }}
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-1.5 justify-end items-center">
                        @if ($listing->merchant_id === auth()->id())
                            <span class="text-[12px] text-[var(--text-muted)] italic">Your listing</span>
                        @else
                            <form method="POST" action="/marketplace/{{ $listing->id }}/buy" class="mt-0 mr-0 mb-0 ml-0">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" title="Buy 1" class="bg-[#059669] text-[#fff] [border:none] pt-1.5 pr-3 pb-1.5 pl-3 rounded-md font-semibold text-[12px] cursor-pointer [font-family:inherit]"
                                       >
                                    Buy
                                </button>
                            </form>

                            <a href="/marketplace/{{ $listing->id }}/propose-trade" title="Propose Trade" class="bg-[var(--surface)] [border:1px_solid_var(--border)] text-[var(--text-secondary)] pt-1.5 pr-3 pb-1.5 pl-3 rounded-md font-semibold text-[12px] no-underline inline-flex items-center"
                              >
                                Trade
                            </a>

                            <a href="/messages/start/{{ $listing->merchant_id }}" title="Message Seller" class="w-8 h-8 rounded-md [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] inline-flex items-center justify-center no-underline hover:bg-[var(--bg)] hover:text-[var(--text)]"
                              
                              
                              >
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center mb-8">
            <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="store" class="w-5 h-5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                No active listings
            </div>
            <div class="text-[13px] text-[var(--text-muted)]">
                @if (auth()->user()->isMerchant())
                    Be the first to <a href="/marketplace/create" class="text-[var(--text)] font-medium">create a listing</a>.
                @else
                    Check back later or visit the <a href="/gears" class="text-[var(--text)] font-medium">Gear Shop</a>.
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ WANTED ADS ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Wanted Ads
        </span>
        <span class="text-[12px] text-[var(--text-muted)]">{{ ($wantedPosts ?? collect())->count() }}</span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
        <a href="/marketplace/wanted/mine" class="text-[12px] text-[var(--text-secondary)] font-medium">
            My Wanted Ads →
        </a>
    </div>

    @if (($wantedPosts ?? collect())->count())
        <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">

            {{-- Header row --}}
            <div class="grid [grid-template-columns:2fr_1.5fr_0.7fr_1fr_1.2fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Wanted Gear</div>
                <div>Posted By</div>
                <div>Qty</div>
                <div>Offering</div>
                <div class="text-right">Action</div>
            </div>

            @foreach ($wantedPosts as $wp)
                <div class="grid [grid-template-columns:2fr_1.5fr_0.7fr_1fr_1.2fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    {{-- Gear --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="tag" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $wp->gear->name }}
                            </div>
                            @if ($wp->note)
                                <div class="text-[11px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                    {{ \Illuminate\Support\Str::limit($wp->note, 40) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Poster --}}
                    <div class="text-[13px] text-[var(--text-secondary)] min-w-0">
                        <div class="whitespace-nowrap overflow-hidden text-ellipsis">
                            <x-user-name :user="$wp->user" />
                        </div>
                        <div class="text-[11px] text-[var(--text-muted)]">{{ $wp->user->roleLabel() }}</div>
                    </div>

                    {{-- Qty --}}
                    <div class="text-[13px] text-[var(--text)] font-medium">
                        ×{{ $wp->quantity }}
                    </div>

                    {{-- Offering --}}
                    <div class="text-[13.5px] text-[var(--text)] font-semibold">
                        ₱ {{ $wp->price_per_unit }}
                    </div>

                    {{-- Action --}}
                    <div class="flex justify-end gap-1.5 items-center">
                        @if ($wp->user_id === auth()->id())
                            <a href="/marketplace/wanted/mine" class="text-[12px] text-[var(--text-secondary)] font-medium no-underline"
                              >
                                View responses →
                            </a>
                        @else
                            <a href="/marketplace/wanted/{{ $wp->id }}/respond" class="btn btn-secondary text-[12.5px] pt-1.5 pr-3 pb-1.5 pl-3">
                                <i data-lucide="hand"></i>
                                <span>I Have This</span>
                            </a>
                            <a href="/messages/start/{{ $wp->user_id }}" title="Message Poster" class="w-8 h-8 rounded-md [border:1px_solid_var(--border)] bg-[var(--surface)] text-[var(--text-secondary)] inline-flex items-center justify-center no-underline hover:bg-[var(--bg)] hover:text-[var(--text)]"
                              
                              
                              >
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center">
            <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="tag" class="w-5 h-5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                No wanted ads yet
            </div>
            <div class="text-[13px] text-[var(--text-muted)] mb-3.5">
                Looking for something specific? Post an ad and we'll notify everyone who owns it.
            </div>
            <a href="/marketplace/wanted/create" class="btn btn-secondary inline-flex">
                <i data-lucide="plus"></i>
                <span>Post Wanted Ad</span>
            </a>
        </div>
    @endif

@endsection