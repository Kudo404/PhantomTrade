@extends('layouts.app')

@section('title', 'Create Listing — Phantom Trade')

@section('content')

    <h1 class="text-[#a78bfa] mt-2 mr-0 mb-1 ml-0">🏪 Create a Listing</h1>
    <p class="text-[#888] mb-5">Put one of your items up for sale on the marketplace.</p>

    @if ($myItems->count())
        <form method="POST" action="/marketplace" class="bg-[#2a2a45] pt-6 pr-6 pb-6 pl-6 rounded-[12px] max-w-[540px] [border:1px_solid_#3a3a5c]"
             >
            @csrf

            <label class="block text-[13px] text-[#aaa] mt-0 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
                Which item?
            </label>
            <select name="gear_id" required class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee]"
                   >
                <option value="">— Pick from your inventory —</option>
                @foreach ($myItems as $item)
                    <option value="{{ $item->gear->id }}" {{ old('gear_id') == $item->gear->id ? 'selected' : '' }}>
                        {{ $item->gear->name }} (you have {{ $item->quantity }})
                    </option>
                @endforeach
            </select>
            @error('gear_id') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

            <label class="block text-[13px] text-[#aaa] mt-3.5 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
                Quantity to sell
            </label>
            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee]"
                  >
            @error('quantity') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

            <label class="block text-[13px] text-[#aaa] mt-3.5 mr-0 mb-1 ml-0 uppercase tracking-[1px]">
                Price per unit ₱
            </label>
            <input type="number" name="price_per_unit" value="{{ old('price_per_unit', 10) }}" min="1" required class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 rounded-md [border:1px_solid_#3a3a5c] bg-[#1a1a2e] text-[#eee]"
                  >
            @error('price_per_unit') <div class="text-[#fca5a5] text-[13px] mt-1">{{ $message }}</div> @enderror

            <div class="mt-2.5 text-[12px] text-[#888]">
                💡 Tip: Buyers will also be able to <strong>propose a trade</strong> for this item.
            </div>

            <button type="submit" class="btn mt-[18px]">🏪 Publish Listing</button>
        </form>
    @else
        <div class="text-[#888] pt-10 pr-10 pb-10 pl-10 text-center bg-[#2a2a45] rounded-[12px]">
            <div class="text-[48px] mb-3">📦</div>
            <p class="mb-4">You don't own any gear to sell.</p>
            <a href="/gears" class="btn">Buy some gear first →</a>
        </div>
    @endif

@endsection