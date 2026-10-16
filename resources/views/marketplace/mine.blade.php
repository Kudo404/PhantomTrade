@extends('layouts.app')

@section('title', 'My Listings — Phantom Trade')

@section('content')

    <div class="flex justify-between items-start mb-5">
        <div>
            <h1 class="text-[#a78bfa] mb-1">📋 My Listings</h1>
            <p class="text-[#888]">Everything you've put up for sale.</p>
        </div>
        <div class="flex gap-2">
            <a href="/marketplace/proposals" class="btn bg-[#3a3a5c] text-[#eee]">🔄 Proposals</a>
            <a href="/marketplace/create" class="btn">+ New Listing</a>
        </div>
    </div>

    @if ($listings->count())
        <div class="mt-5">
            @foreach ($listings as $listing)
                <div class="card mb-2.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="mt-0 mr-0 mb-1.5 ml-0 text-[#a78bfa] text-[17px]">
                                {{ $listing->gear->name }}
                            </h2>
                            <span class="type-tag">{{ $listing->gear->type }}</span>

                            <div class="text-[13px] text-[#ccc] mt-2.5">
                                ₱ <strong>{{ $listing->price_per_unit }}</strong> per unit
                                &nbsp;·&nbsp;
                                <strong>{{ $listing->quantity }}</strong> remaining
                            </div>

                            <div class="text-[11px] text-[#666] mt-1.5">
                                Posted {{ $listing->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div class="text-right">
                            <span style="--tw-inline-background:{{ $listing->statusColor() }}" class="bg-[var(--tw-inline-background)] text-[#000] pt-1 pr-3 pb-1 pl-3 rounded-[20px] text-[11px] font-bold uppercase">
                                {{ str_replace('_', ' ', $listing->status) }}
                            </span>

                            @if ($listing->status === 'active')
                                <form method="POST" action="/marketplace/{{ $listing->id }}/cancel" class="mt-2.5 mr-0 mb-0 ml-0">
                                    @csrf
                                    <button type="submit" class="bg-[#f87171] text-[#000] [border:none] pt-1.5 pr-3 pb-1.5 pl-3 rounded-md font-bold text-[12px] cursor-pointer"
                                           >
                                        🗑️ Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-[#888] pt-[60px] pr-10 pb-[60px] pl-10 text-center bg-[#2a2a45] rounded-[12px]">
            <div class="text-[48px] mb-3">📋</div>
            <p class="mb-4">You haven't listed anything yet.</p>
            <a href="/marketplace/create" class="btn">Create your first listing →</a>
        </div>
    @endif

@endsection