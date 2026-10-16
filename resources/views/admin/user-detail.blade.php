@extends('layouts.app')

@section('title', $user->name . ' — Admin — Phantom Trade')

@section('content')

    <div class="max-w-[900px] mt-0 mr-auto mb-0 ml-auto">

        {{-- Header --}}
        <div class="card pt-6 pr-6 pb-6 pl-6 mb-6">
            <div class="flex items-center gap-5 flex-wrap">
                <div class="w-16 h-16 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center text-[24px] font-semibold shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="grow min-w-[200px]">
                    <h1 class="text-[20px] font-semibold text-[var(--text)] mb-1">
                        <x-user-name :user="$user" />
                    </h1>
                    <div class="text-[13px] text-[var(--text-secondary)]">
                        {{ $user->email }}
                    </div>
                    @if ($user->hunter_name && $user->hunter_name !== $user->name)
                        <div class="text-[12px] text-[var(--text-muted)] mt-1">
                            Hunter name: <strong class="text-[var(--text-secondary)]">{{ $user->hunter_name }}</strong>
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <span class="type-tag text-[11px] pt-1 pr-3 pb-1 pl-3">{{ $user->roleLabel() }}</span>
                    <div class="text-[11.5px] text-[var(--text-muted)] mt-1.5">
                        Joined {{ $user->created_at->format('M j, Y') }}
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="success-banner">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="error-banner">{{ session('error') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(140px,_1fr))] gap-3 mb-6">
            <div class="card pt-4 pr-4 pb-4 pl-4">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Balance</div>
                <div class="text-[22px] font-semibold text-[var(--text)] mt-1.5">₱ {{ $user->ecto }}</div>
            </div>
            <div class="card pt-4 pr-4 pb-4 pl-4">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Inventory</div>
                <div class="text-[22px] font-semibold text-[var(--text)] mt-1.5">{{ $inventoryCount }}</div>
            </div>
            <div class="card pt-4 pr-4 pb-4 pl-4">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Purchases</div>
                <div class="text-[22px] font-semibold text-[var(--text)] mt-1.5">{{ $purchasesCount }}</div>
            </div>
            <div class="card pt-4 pr-4 pb-4 pl-4">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Trades</div>
                <div class="text-[22px] font-semibold text-[var(--text)] mt-1.5">{{ $tradesCount }}</div>
            </div>
        </div>

        {{-- Update Role --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="shield" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Role & Tier
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <form method="POST" action="/admin/users/{{ $user->id }}" class="card pt-5 pr-5 pb-5 pl-5 mb-6">
            @csrf

            <div class="grid [grid-template-columns:1fr_1fr] gap-5 mb-4">

                {{-- Role --}}
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Role
                    </label>
                    <select name="role" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none cursor-pointer"
                           >
                        <option value="civilian"     {{ $user->role === 'civilian'     ? 'selected' : '' }}>Civilian</option>
                        <option value="ghost_hunter" {{ $user->role === 'ghost_hunter' ? 'selected' : '' }}>Ghost Hunter</option>
                        <option value="merchant"     {{ $user->role === 'merchant'     ? 'selected' : '' }}>Merchant</option>
                        <option value="admin"        {{ $user->role === 'admin'        ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                {{-- Hunter tier (only if hunter) --}}
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Hunter Tier
                    </label>
                    @if ($user->isGhostHunter())
                        <select name="hunter_tier" class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] [font-family:inherit] outline-none cursor-pointer"
                               >
                            <option value="D" {{ $user->hunter_tier === 'D' ? 'selected' : '' }}>Class D</option>
                            <option value="C" {{ $user->hunter_tier === 'C' ? 'selected' : '' }}>Class C</option>
                            <option value="B" {{ $user->hunter_tier === 'B' ? 'selected' : '' }}>Class B</option>
                            <option value="A" {{ $user->hunter_tier === 'A' ? 'selected' : '' }}>Class A</option>
                            <option value="S" {{ $user->hunter_tier === 'S' ? 'selected' : '' }}>Class S</option>
                        </select>
                    @else
                        <div class="pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_dashed_var(--border)] rounded-md text-[13px] text-[var(--text-muted)]">
                            Tier only applies to Ghost Hunters.
                        </div>
                    @endif
                </div>

            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn">
                    <i data-lucide="save"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>

        {{-- Applications history --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Application History
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <div class="card overflow-hidden">
            @forelse ($applications as $app)
                <a href="/admin/applications/{{ $app->id }}"
                  
                  
                   class="flex items-center justify-between gap-3 pt-3.5 pr-[18px] pb-3.5 pl-[18px] [border-bottom:1px_solid_var(--border-subtle)] no-underline [transition:background_0.12s]  hover:bg-[var(--bg)]">
                    <div>
                        <div class="text-[13px] font-medium text-[var(--text)]">
                            {{ $app->targetRoleLabel() }} Application
                        </div>
                        <div class="text-[11.5px] text-[var(--text-muted)]">
                            Submitted {{ $app->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span style="--tw-inline-background:{{ $app->statusColor() }}22;--tw-inline-color:{{ $app->statusColor() }}" class="bg-[var(--tw-inline-background)] text-[var(--tw-inline-color)] pt-[3px] pr-2.5 pb-[3px] pl-2.5 rounded-[20px] text-[10.5px] font-semibold uppercase tracking-[0.05em]">
                        {{ $app->statusLabel() }}
                    </span>
                </a>
            @empty
                <div class="pt-8 pr-5 pb-8 pl-5 text-center text-[var(--text-muted)] text-[13px]">
                    No applications submitted.
                </div>
            @endforelse
        </div>

    </div>

@endsection