```blade
@extends('layouts.app')

@section('title', $gear->name . ' — Phantom Trade')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- Back navigation --}}
    <div class="mb-5">
        <a href="/gears"
           class="inline-flex items-center gap-2 text-sm text-[var(--text-secondary)] hover:text-[var(--text)] transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Gear Shop
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-green-700 bg-green-500/10 px-4 py-3 text-sm text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-700 bg-red-500/10 px-4 py-3 text-sm text-red-400">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Product layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- Product image --}}
        <div class="card p-3">
            <div class="relative aspect-square overflow-hidden rounded-xl bg-[var(--bg)] flex items-center justify-center">

                @if ($gear->image_path)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($gear->image_path) }}"
                        alt="{{ $gear->name }}"
                        class="w-full h-full object-contain"
                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                    >

                    <div class="hidden absolute inset-0 flex-col items-center justify-center gap-3 text-[var(--text-muted)]">
                        <i data-lucide="image-off" class="w-12 h-12"></i>
                        <p class="text-sm">Product image unavailable</p>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center gap-4 p-6 text-center text-[var(--text-muted)]">
                        <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-[var(--card)]">
                            @if ($gear->type === 'trap')
                                <i data-lucide="target" class="w-12 h-12"></i>
                            @elseif ($gear->type === 'ward')
                                <i data-lucide="shield" class="w-12 h-12"></i>
                            @elseif ($gear->type === 'anchor')
                                <i data-lucide="anchor" class="w-12 h-12"></i>
                            @else
                                <i data-lucide="scan-line" class="w-12 h-12"></i>
                            @endif
                        </div>

                        <div>
                            <p class="text-base font-semibold text-[var(--text)]">
                                {{ $gear->name }}
                            </p>
                            <p class="mt-1 text-xs">
                                No product photo uploaded yet
                            </p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Product information --}}
        <div class="flex flex-col gap-5">

            <div class="card p-6">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <span class="type-tag">{{ $gear->type }}</span>
                    <span class="text-xs text-[var(--text-muted)]">
                        Gear #{{ $gear->id }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[var(--text)] mb-3">
                    {{ $gear->name }}
                </h1>

                <p class="text-sm leading-7 text-[var(--text-secondary)]">
                    {{ $gear->description }}
                </p>

                <div class="grid grid-cols-2 gap-4 mt-6">

                    {{-- Price --}}
                    <div class="rounded-xl bg-[var(--bg)] p-4">
                        <div class="flex items-center gap-2 mb-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <i data-lucide="gem" class="w-4 h-4"></i>
                            Price
                        </div>

                        <p class="text-2xl font-semibold text-[var(--text)]">
                            ₱{{ number_format($gear->ecto_cost) }}
                        </p>

                        <p class="mt-1 text-xs text-[var(--text-muted)]">
                            Per unit
                        </p>
                    </div>

                    {{-- Energy --}}
                    <div class="rounded-xl bg-[var(--bg)] p-4">
                        <div class="flex items-center gap-2 mb-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
                            Energy
                        </div>

                        <p class="text-2xl font-semibold text-[var(--text)]">
                            {{ $gear->energy_cost }}
                        </p>

                        <p class="mt-1 text-xs text-[var(--text-muted)]">
                            Consumed per hunt
                        </p>
                    </div>

                </div>
            </div>

            {{-- Purchase card --}}
            <div class="card p-5">
                <div class="flex items-start gap-3 mb-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--bg)]">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-[var(--text)]"></i>
                    </div>

                    <div>
                        <h2 class="text-sm font-semibold text-[var(--text)]">
                            Add to your inventory
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-[var(--text-secondary)]">
                            Purchase this gear for your next paranormal operation.
                        </p>
                    </div>
                </div>

                <form method="GET" action="/gears/{{ $gear->id }}/checkout">
                    <button type="submit"
                            class="btn w-full flex items-center justify-center gap-2">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        Buy for ₱{{ number_format($gear->ecto_cost) }}
                    </button>
                </form>

                <p class="mt-3 text-center text-xs text-[var(--text-muted)]">
                    Make sure you have enough ecto before purchasing.
                </p>
            </div>

        </div>
    </div>
</div>

@endsection
```