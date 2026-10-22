@extends('layouts.app')

@section('title', 'Admin Panel — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="mb-7">
        <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
            Admin Panel
        </h1>
        <p class="text-[13.5px] text-[var(--text-secondary)]">
            Overview of the entire Phantom Trade system.
        </p>
        <a href="/admin/top-ups" class="inline-flex items-center gap-2 mt-3 rounded-lg border border-[var(--border)] px-4 py-2 text-sm no-underline">
            <i data-lucide="wallet" class="w-4 h-4"></i> Review demo top-up requests
        </a>
    </div>

    {{-- ═══ ALERT: Pending Applications ═══ --}}
    @if ($pendingApps > 0)
        <a href="/admin/applications" class="flex items-center gap-3.5 pt-4 pr-5 pb-4 pl-5 bg-[#fffbeb] [border:1px_solid_#fde68a] rounded-lg mb-6 no-underline [transition:all_0.12s] hover:bg-[#fef3c7]"
          
          >
            <div class="w-9 h-9 rounded-full bg-[#fbbf24] flex items-center justify-center shrink-0">
                <i data-lucide="clock" class="w-[18px] h-[18px] text-[#ffffff]"></i>
            </div>
            <div class="grow">
                <div class="text-[13.5px] font-semibold text-[#92400e]">
                    {{ $pendingApps }} pending {{ Str::plural('application', $pendingApps) }} waiting for review
                </div>
                <div class="text-[12px] text-[#a16207] mt-0.5">
                    Click to review role applications.
                </div>
            </div>
            <i data-lucide="arrow-right" class="w-[18px] h-[18px] text-[#a16207]"></i>
        </a>
    @endif

    {{-- ═══ KPI STRIP ═══ --}}
        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(200px,_1fr))] gap-0 [border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] overflow-hidden mb-8">
        {{-- Total Users --}}
        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Total Users
                </span>
                <i data-lucide="users" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                {{ $totalUsers }}
            </div>
            <div class="text-[11.5px] text-[var(--text-muted)] mt-1.5 leading-[1.5]">
                {{ $totalCivilians }} {{ Str::plural('civilian', $totalCivilians) }} ·
                {{ $totalHunters }} {{ Str::plural('hunter', $totalHunters) }} ·
                {{ $totalMerchants }} {{ Str::plural('merchant', $totalMerchants) }} ·
                {{ $totalAdmins }} {{ Str::plural('admin', $totalAdmins) }}
            </div>
        </div>

        {{-- Ecto in System --}}
        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    In System
                </span>
                <i data-lucide="wallet" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                {{ number_format($totalEcto) }}
            </div>
            <div class="text-[11.5px] text-[var(--text-muted)] mt-1.5">
                avg {{ $avgEcto }} per user
            </div>
        </div>

        {{-- Active Listings --}}
        <div class="pt-5 pr-6 pb-5 pl-6 [border-right:1px_solid_var(--border)]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Active Listings
                </span>
                <i data-lucide="store" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                {{ $activeListings }}
            </div>
            <div class="text-[11.5px] text-[var(--text-muted)] mt-1.5 leading-[1.5]">
                {{ $soldOutListings }} sold out ·
                {{ $cancelledListings }} {{ Str::plural('cancelled', $cancelledListings) }}
            </div>
        </div>

        {{-- Purchases --}}
        <div class="pt-5 pr-6 pb-5 pl-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Purchases
                </span>
                <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                {{ $totalPurchases }}
            </div>
            <div class="text-[11.5px] text-[var(--text-muted)] mt-1.5 leading-[1.5]">
                {{ $shopPurchases }} shop · {{ $marketPurchases }} market ·
                {{ $totalTrades }} {{ Str::plural('trade', $totalTrades) }}
            </div>
        </div>
    </div>

    {{-- ═══ QUICK ACTIONS ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <i data-lucide="zap" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Quick Actions
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    @php
        $actions = [
            ['url' => '/admin/applications', 'icon' => 'inbox',      'label' => 'Applications', 'badge' => $pendingApps],
            ['url' => '/admin/users',        'icon' => 'users',      'label' => 'Manage Users', 'badge' => null],
            ['url' => '/gears/create',       'icon' => 'plus-circle','label' => 'Add Gear',     'badge' => null],
            ['url' => '/gears',              'icon' => 'package',    'label' => 'Gear Catalog', 'badge' => null],
            ['url' => '/marketplace',        'icon' => 'store',      'label' => 'Marketplace',  'badge' => null],
            ['url' => '/admin/settings',     'icon' => 'settings',   'label' => 'Settings',     'badge' => null],
        ];
    @endphp

    <div class="grid [grid-template-columns:repeat(auto-fill,_minmax(180px,_1fr))] gap-3 mb-8">
        @foreach ($actions as $a)
            <a href="{{ $a['url'] }}"
              
              
               class="relative flex items-center gap-2.5 pt-3.5 pr-4 pb-3.5 pl-4 bg-[var(--surface)] [border:1px_solid_var(--border)] rounded-md text-[var(--text)] text-[13px] font-medium no-underline [transition:all_0.12s]  hover:bg-[var(--bg)] hover:[border-color:var(--text-muted)]">
                <i data-lucide="{{ $a['icon'] }}" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                <span>{{ $a['label'] }}</span>
                @if ($a['badge'] && $a['badge'] > 0)
                    <span class="ml-auto bg-[#fbbf24] text-[#000] text-[10px] font-bold pt-0.5 pr-2 pb-0.5 pl-2 rounded-[10px]">
                        {{ $a['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- ═══ TWO COLUMN: Applications + Users ═══ --}}
    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(340px,_1fr))] gap-4">

        {{-- Recent Applications --}}
        <div>
            <div class="flex items-center gap-3 mb-3.5">
                <i data-lucide="file-text" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Recent Applications
                </span>
                <div class="grow h-[1px] bg-[var(--border)]"></div>
                <a href="/admin/applications" class="text-[11.5px] text-[var(--text-secondary)] font-medium">View all</a>
            </div>

            <div class="card overflow-hidden">
                @forelse ($recentApplications as $app)
                    <a href="/admin/applications/{{ $app->id }}"
                      
                      
                       class="flex items-center gap-3 pt-3.5 pr-[18px] pb-3.5 pl-[18px] [border-bottom:1px_solid_var(--border-subtle)] no-underline [transition:background_0.12s]  hover:bg-[var(--bg)]">
                        <div class="w-8 h-8 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[12px] shrink-0">
                            {{ strtoupper(substr($app->user->name, 0, 1)) }}
                        </div>
                        <div class="grow min-w-0">
                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                <x-user-name :user="$app->user" />
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)]">
                                {{ $app->targetRoleLabel() }} · {{ $app->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span style="--tw-inline-background:{{ $app->statusColor() }}22;--tw-inline-color:{{ $app->statusColor() }}" class="bg-[var(--tw-inline-background)] text-[var(--tw-inline-color)] pt-[3px] pr-2.5 pb-[3px] pl-2.5 rounded-[20px] text-[10.5px] font-semibold uppercase tracking-[0.05em]">
                            {{ $app->statusLabel() }}
                        </span>
                    </a>
                @empty
                    <div class="pt-8 pr-5 pb-8 pl-5 text-center text-[var(--text-muted)] text-[13px]">
                        No applications yet.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Users --}}
        <div>
            <div class="flex items-center gap-3 mb-3.5">
                <i data-lucide="user-plus" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Newest Users
                </span>
                <div class="grow h-[1px] bg-[var(--border)]"></div>
                <a href="/admin/users" class="text-[11.5px] text-[var(--text-secondary)] font-medium">View all</a>
            </div>

            <div class="card overflow-hidden">
                @foreach ($recentUsers as $u)
                    <a href="/admin/users/{{ $u->id }}"
                      
                      
                       class="flex items-center gap-3 pt-3.5 pr-[18px] pb-3.5 pl-[18px] [border-bottom:1px_solid_var(--border-subtle)] no-underline [transition:background_0.12s]  hover:bg-[var(--bg)]">
                        <div class="w-8 h-8 rounded-full bg-[var(--bg)] [border:1px_solid_var(--border)] text-[var(--text-secondary)] flex items-center justify-center font-semibold text-[12px] shrink-0">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div class="grow min-w-0">
                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                <x-user-name :user="$u" />
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $u->email }}
                            </div>
                        </div>
                        <span class="bg-[var(--bg)] text-[var(--text-secondary)] pt-[3px] pr-2.5 pb-[3px] pl-2.5 rounded text-[10px] font-semibold uppercase tracking-[0.05em]">
                            {{ $u->roleLabel() }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

@endsection