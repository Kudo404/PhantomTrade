@extends('layouts.app')

@section('title', 'Respond to Wanted Ad — Phantom Trade')

@section('content')

    <div class="mb-7">
        <h1 class="mb-1 text-2xl font-semibold tracking-[-0.02em] text-slate-900">
            Respond to Wanted Ad
        </h1>
        <p class="text-[13.5px] text-slate-500">
            Offer your item to this buyer.
        </p>
    </div>

    <div class="max-w-[640px]">

        {{-- What they want --}}
        <div class="card mb-5 p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-grow">
                    <div class="mb-2 text-[11px] font-semibold uppercase tracking-[0.06em] text-slate-400">
                        They're looking for
                    </div>
                    <div class="mb-1.5 text-lg font-semibold text-slate-900">
                        {{ $post->gear->name }}
                    </div>
                    <div class="text-[13px] text-slate-500">
                        {{ $post->quantity }}× at ₱ {{ $post->price_per_unit }} each
                        · by <strong class="font-semibold text-slate-900"><x-user-name :user="$post->user" /></strong>
                    </div>
                    @if ($post->note)
                        <div class="mt-3 border-t border-slate-100 pt-3 text-xs italic text-slate-500">
                            "{{ $post->note }}"
                        </div>
                    @endif
                </div>

                <a href="/messages/start/{{ $post->user_id }}"
                   class="inline-flex shrink-0 items-center gap-[5px] whitespace-nowrap rounded-md border border-slate-200 bg-slate-50 px-3 py-[7px] text-xs font-medium text-slate-500 no-underline hover:border-slate-400 hover:bg-white">
                    <i data-lucide="message-circle" class="h-[13px] w-[13px]"></i>
                    <span>Message Poster</span>
                </a>
            </div>
        </div>

        {{-- Response form --}}
        <form method="POST" action="/marketplace/wanted/{{ $post->id }}/respond" class="card p-7">
            @csrf

            <div class="mb-5 flex gap-2.5 rounded-md bg-slate-50 px-3.5 py-3 text-xs leading-[1.5] text-slate-500">
                <i data-lucide="package" class="mt-px h-[15px] w-[15px] shrink-0 text-slate-400"></i>
                <div>
                    You have <strong class="font-semibold text-slate-900">{{ $myInventory->quantity }}×</strong> {{ $myInventory->gear->name }} in your inventory.
                </div>
            </div>

            <div class="mb-5 grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.05em] text-slate-500">
                        Offering quantity
                    </label>
                    <input type="number" name="offered_quantity"
                           value="{{ old('offered_quantity', min($post->quantity, $myInventory->quantity)) }}"
                           min="1" max="{{ $myInventory->quantity }}" required
                           class="w-full rounded-md border border-slate-200 bg-white px-3 py-2.5 text-[13.5px] text-slate-900 [font-family:inherit] outline-none">
                    @error('offered_quantity') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.05em] text-slate-500">
                        Your price per unit ₱
                    </label>
                    <input type="number" name="price_per_unit"
                           value="{{ old('price_per_unit', $post->price_per_unit) }}"
                           min="1" required
                           class="w-full rounded-md border border-slate-200 bg-white px-3 py-2.5 text-[13.5px] text-slate-900 [font-family:inherit] outline-none">
                    @error('price_per_unit') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-5">
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.05em] text-slate-500">
                    Note <span class="font-normal normal-case text-slate-400">(optional)</span>
                </label>
                <textarea name="note" maxlength="500"
                          placeholder="Any message for the buyer?"
                          class="min-h-[80px] w-full resize-y rounded-md border border-slate-200 bg-white px-3 py-2.5 text-[13.5px] text-slate-900 [font-family:inherit] outline-none">{{ old('note') }}</textarea>
                @error('note') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
            </div>

            <div class="flex justify-end gap-2.5">
                <a href="/marketplace" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn">
                    <i data-lucide="send"></i>
                    <span>Send Response</span>
                </button>
            </div>
        </form>
    </div>

@endsection