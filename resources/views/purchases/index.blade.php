@extends('layouts.app')

@section('title', 'Recent Purchases — Phantom Trade')

@section('content')

    <h1 class="text-[#a78bfa] mb-1">🧾 Recent Purchases</h1>
    <p class="text-[#888] mb-6">Everything you've bought.</p>

    {{-- Summary --}}
    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-[30px]">
        <div class="card">
            <div class="text-[#888] text-[13px] uppercase tracking-[1px]">Total Spent</div>
            <div class="text-[32px] font-bold text-[#a78bfa] mt-1.5">₱ {{ $totalSpent }}</div>
        </div>
        <div class="card">
            <div class="text-[#888] text-[13px] uppercase tracking-[1px]">Items Bought</div>
            <div class="text-[32px] font-bold text-[#a78bfa] mt-1.5">{{ $totalItems }}</div>
        </div>
        <div class="card">
            <div class="text-[#888] text-[13px] uppercase tracking-[1px]">Unique Gear</div>
            <div class="text-[32px] font-bold text-[#a78bfa] mt-1.5">{{ $uniqueCount }}</div>
        </div>
    </div>

    {{-- History --}}
    @if ($purchases->count())
        @foreach ($purchases as $p)
            <div class="card mb-2 pt-3.5 pr-[18px] pb-3.5 pl-[18px]">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="mb-1">
                            <strong class="text-[#a78bfa] text-[15px]">
                                {{ $p->quantity }}× {{ $p->gear->name }}
                            </strong>
                        </div>
                        <div class="text-[12px] text-[#888]">
                            {{ $p->sourceLabel() }}
                            @if ($p->seller)
                                · from <span class="text-[#c084fc]">{{ $p->seller->name }}</span>
                            @endif
                            · {{ $p->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[18px] text-[#c084fc] font-bold">
                            ₱ {{ $p->totalPrice() }}
                        </div>
                        
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-[#888] pt-[60px] pr-10 pb-[60px] pl-10 text-center bg-[#2a2a45] rounded-[12px]">
            <div class="text-[48px] mb-3">🧾</div>
            <p class="mb-4">You haven't bought anything yet.</p>
            <a href="/gears" class="btn">Browse the Gear Shop →</a>
        </div>
    @endif

@endsection