@extends('layouts.app')

@section('title', 'Add Gear — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="mb-7">
        <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
            Add New Gear
        </h1>
        <p class="text-[13.5px] text-[var(--text-secondary)]">
            Register a new gadget to the shop catalog.
        </p>
    </div>

    <div class="max-w-[640px]">
        <form method="POST" action="/gears" class="card pt-7 pr-7 pb-7 pl-7">
            @csrf

            {{-- Name --}}
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                    Name
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                       placeholder="e.g. Spirit Camera" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                      >
                @error('name') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Type --}}
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                    Type
                </label>
                <select name="type" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none cursor-pointer"
                       >
                    <option value="">Select a type…</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
                @error('type') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                    Description
                </label>
                <textarea name="description" required maxlength="1000"
                          placeholder="What does this gear do?" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none min-h-[100px] resize-y"
                         >{{ old('description') }}</textarea>
                @error('description') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Costs side by side --}}
            <div class="grid [grid-template-columns:1fr_1fr] gap-4 mb-6">
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Cost ₱
                    </label>
                    <input type="number" name="ecto_cost" value="{{ old('ecto_cost', 10) }}" min="0" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                          >
                    @error('ecto_cost') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Energy Cost 💚
                    </label>
                    <input type="number" name="energy_cost" value="{{ old('energy_cost', 5) }}" min="0" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none"
                          >
                    @error('energy_cost') <div class="text-[12px] text-[var(--danger)] mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2.5 justify-end">
                <a href="/gears" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn">
                    <i data-lucide="plus"></i>
                    <span>Add to Catalog</span>
                </button>
            </div>
        </form>
    </div>

@endsection