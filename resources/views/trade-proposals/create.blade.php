@extends('layouts.app')

@section('title', 'Propose a Trade — Phantom Trade')

@section('content')

    <h1 class="text-[#a78bfa] mt-2 mr-0 mb-1 ml-0">🔄 Propose a Trade</h1>
    <p class="text-[#888] mb-5">
        Offer one of your items in exchange for this listing.
    </p>

    {{-- What they want --}}
    <div class="card mb-5 [border-left:3px_solid_#a78bfa]">
        <div class="text-[12px] text-[#888] uppercase tracking-[1px]">You're trading for</div>
        <div class="text-[20px] font-bold text-[#a78bfa] mt-1.5">
            {{ $listing->gear->name }}
        </div>
        <div class="text-[13px] text-[#ccc] mt-1.5">
            From <strong>{{ $listing->merchant->name }}</strong> · Listing price ₱ {{ $listing->price_per_unit }}
        </div>
    </div>

    @if ($myItems->count())
        <form method="POST" action="/marketplace/{{ $listing->id }}/propose-trade" class="bg-[#2a2a45] pt-6 pr-6 pb-6 pl-6 rounded-[12px] max-w-[540px] [border:1px_solid_#3a3a5c]"
             >
            @csrf

            <label class="block text-[13px] text-[#aaa] mt-0 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
                What you're offering
            </label>
            <select name="offered_gear_id" required class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee]"
                   >
                <option value="">— Pick from your inventory —</option>
                @foreach ($myItems as $item)
                    <option value="{{ $item->gear->id }}" {{ old('offered_gear_id') == $item->gear->id ? 'selected' : '' }}>
                        {{ $item->gear->name }} (you have {{ $item->quantity }})
                    </option>
                @endforeach
            </select>
            @error('offered_gear_id') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

            <label class="block text-[13px] text-[#aaa] mt-3.5 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
                How many?
            </label>
            <input type="number" name="offered_quantity" value="{{ old('offered_quantity', 1) }}" min="1" required class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee]"
                  >
            @error('offered_quantity') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

            <label class="block text-[13px] text-[#aaa] mt-3.5 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
                Note (optional)
            </label>
            <textarea name="note" maxlength="500" placeholder="Any message for the merchant..." class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee] min-h-20"
                     >{{ old('note') }}</textarea>
            @error('note') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

            <button type="submit" class="btn mt-[18px]">🔄 Send Proposal</button>
        </form>
    @else
        <div class="text-[#888] pt-10 pr-10 pb-10 pl-10 text-center bg-[#2a2a45] rounded-[12px]">
            <div class="text-[48px] mb-3">📦</div>
            <p class="mb-4">You don't own any gear to offer.</p>
            <a href="/gears" class="btn">Buy some gear →</a>
        </div>
    @endif

@endsection