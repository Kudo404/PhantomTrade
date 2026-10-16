@extends('layouts.app')

@section('title', 'Post a Wanted Ad — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="mb-7">
        <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
            Post a Wanted Ad
        </h1>
        <p class="text-[13.5px] text-[var(--text-secondary)]">
            Tell the marketplace what you're looking for. Everyone who owns it will be notified.
        </p>
    </div>

    <div class="max-w-[640px]">
        <form method="POST" action="/marketplace/wanted" class="card pt-7 pr-7 pb-7 pl-7">
            @csrf

            {{-- Gear --}}
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                    What are you looking for?
                </label>
                <select name="gear_id" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none cursor-pointer"
                       >
                    <option value="">Select gear…</option>
                    @foreach ($allGear as $g)
                        <option value="{{ $g->id }}" {{ old('gear_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->name }} — base ₱ {{ $g->ecto_cost }}
                        </option>
                    @endforeach
                </select>
                @error('gear_id') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Quantity + Price side by side --}}
            <div class="grid [grid-template-columns:1fr_1fr] gap-4 mb-5">
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Quantity
                    </label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="999" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                          >
                    @error('quantity') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Price per unit ₱
                    </label>
                    <input type="number" name="price_per_unit" value="{{ old('price_per_unit', 15) }}" min="1" max="99999" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                          >
                    @error('price_per_unit') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Note --}}
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                    Note <span class="text-[var(--text-muted)] normal-case font-normal">(optional)</span>
                </label>
                <textarea name="note" maxlength="500"
                          placeholder="Any details? When do you need it by? Willing to negotiate?" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none min-h-[90px] resize-y"
                         >{{ old('note') }}</textarea>
                @error('note') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Info box --}}
            <div class="flex gap-2.5 pt-3 pr-3.5 pb-3 pl-3.5 bg-[var(--bg)] rounded-[var(--radius-sm)] mb-5 text-[12.5px] text-[var(--text-secondary)] leading-[1.5]">
                <i data-lucide="info" class="w-[15px] h-[15px] shrink-0 mt-[1px] text-[var(--text-muted)]"></i>
                <div>
                    This will notify every user who owns this gear. They can respond with their offer, and you choose who to accept.
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2.5 justify-end">
                <a href="/marketplace" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn">
                    <i data-lucide="send"></i>
                    <span>Post Wanted Ad</span>
                </button>
            </div>
        </form>
    </div>

@endsection