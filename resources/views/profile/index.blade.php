@extends('layouts.app')

@section('title', 'My Profile — Phantom Trade')

@section('content')

    @php
        $user    = auth()->user();
        $initial = strtoupper(substr($user->name, 0, 1));
    @endphp

    {{-- ═══ HERO ═══ --}}
    <div class="card pt-7 pr-7 pb-7 pl-7 mb-6">
        <div class="flex items-center gap-6 flex-wrap">

            <div class="w-20 h-20 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center text-[32px] font-semibold shrink-0">
                {{ $initial }}
            </div>

            <div class="grow min-w-[200px]">
                <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                    <x-user-name :user="$user" />
                </h1>
                <div class="text-[13.5px] text-[var(--text-secondary)] mb-2.5">
                    {{ $user->email }}
                </div>

                <div class="flex items-center gap-3.5 flex-wrap">
                    <span class="bg-[var(--bg)] text-[var(--text-secondary)] pt-[3px] pr-2.5 pb-[3px] pl-2.5 rounded uppercase tracking-[0.05em] font-semibold text-[10.5px]">
                        {{ $user->roleLabel() }}
                    </span>

                    @if ($user->hasLocation())
                        <span class="inline-flex items-center gap-[5px] text-[12.5px] text-[var(--text-secondary)]">
                            <i data-lucide="map-pin" class="w-[13px] h-[13px]"></i>
                            {{ $user->locationLabel() }}
                        </span>
                    @else
                        <a href="/profile/location" class="inline-flex items-center gap-[5px] text-[12.5px] text-[var(--text-secondary)] no-underline">
                            <i data-lucide="map-pin" class="w-[13px] h-[13px]"></i>
                            Set your location
                        </a>
                    @endif

                    <span class="inline-flex items-center gap-[5px] text-[12.5px] text-[var(--text-muted)]">
                        <i data-lucide="calendar" class="w-[13px] h-[13px]"></i>
                        Joined {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>

            <div class="pt-4 pr-6 pb-4 pl-6 bg-[var(--bg)] rounded-[var(--radius)] min-w-40 text-right">
                <div class="text-[10.5px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Balance
                </div>
                <div class="text-[28px] font-semibold text-[var(--text)] tracking-[-0.02em] mt-1">
                    ₱ {{ $user->ecto }}
                </div>
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
            ['url' => '/gears',                    'icon' => 'store',            'label' => 'Gear Shop'],
            ['url' => '/marketplace',              'icon' => 'shopping-bag',     'label' => 'Marketplace'],
            ['url' => '/inventory',                'icon' => 'package',          'label' => 'My Inventory'],
            ['url' => '/purchases',                'icon' => 'receipt',          'label' => 'Purchases'],
            ['url' => '/messages',                 'icon' => 'message-circle',   'label' => 'Messages'],
            ['url' => '/marketplace/my-proposals', 'icon' => 'arrow-left-right', 'label' => 'My Proposals'],
            ['url' => '/marketplace/wanted/mine',  'icon' => 'tag',              'label' => 'My Wanted Ads'],
            ['url' => '/profile/location',         'icon' => 'map-pin',          'label' => 'Set Location'],
        ];

        if ($user->isGhostHunter()) {
            $actions[] = ['url' => '/loadout', 'icon' => 'backpack', 'label' => 'My Loadout'];

            if ($user->license_number) {
                $actions[] = ['url' => '/profile/license', 'icon' => 'shield-check', 'label' => 'View License'];
            }
        }

        if ($user->isMerchant()) {
            $actions[] = ['url' => '/marketplace/proposals', 'icon' => 'inbox', 'label' => 'Incoming Proposals'];
            $actions[] = ['url' => '/marketplace/mine',      'icon' => 'list',  'label' => 'My Listings'];
        }

        if ($user->isAdmin()) {
            $actions[] = ['url' => '/admin', 'icon' => 'shield', 'label' => 'Admin Panel'];
            $actions[] = ['url' => '/gears/create', 'icon' => 'plus-circle', 'label' => 'Add Gear'];
        }
    @endphp

    <div class="grid [grid-template-columns:repeat(auto-fill,_minmax(160px,_1fr))] gap-2.5 mb-8">
        @foreach ($actions as $a)
            <a href="{{ $a['url'] }}"
              
              
               class="flex items-center gap-2.5 pt-3.5 pr-4 pb-3.5 pl-4 bg-[var(--surface)] [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] text-[var(--text)] text-[13px] font-medium no-underline [transition:all_0.12s]  hover:bg-[var(--bg)] hover:[border-color:var(--text-muted)]">
                <i data-lucide="{{ $a['icon'] }}" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                <span>{{ $a['label'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- ═══ INVENTORY ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <i data-lucide="package" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Inventory
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-8">
        @php
            $inventoryStats = [
                ['label' => 'Items Owned',     'value' => $totalItems,              'icon' => 'package'],
                ['label' => 'Unique Gear',     'value' => $uniqueGear,              'icon' => 'layers'],
                ['label' => 'Inventory Value', 'value' => '₱ ' . $inventoryValue,  'icon' => 'wallet'],
                ['label' => 'Equipped',        'value' => $equippedCount,           'icon' => 'backpack'],
            ];
        @endphp

        @foreach ($inventoryStats as $s)
            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                        {{ $s['label'] }}
                    </span>
                    <i data-lucide="{{ $s['icon'] }}" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                    {{ $s['value'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ BUYING ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Buying Activity
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-8">
        <div class="card pt-5 pr-5 pb-5 pl-5">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Spent</span>
                <i data-lucide="wallet" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">₱ {{ $totalSpent }}</div>
        </div>

        <div class="card pt-5 pr-5 pb-5 pl-5">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Items Bought</span>
                <i data-lucide="receipt" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $itemsBought }}</div>
        </div>
    </div>

    {{-- ═══ TRADING ═══ --}}
    <div class="flex items-center gap-3 mb-3.5">
        <i data-lucide="arrow-left-right" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Trade Proposals Sent
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(160px,_1fr))] gap-4 mb-8">
        <div class="card pt-5 pr-5 pb-5 pl-5">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Sent</span>
                <i data-lucide="send" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $proposalsSent }}</div>
        </div>

        <div class="card pt-5 pr-5 pb-5 pl-5">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Pending</span>
                <i data-lucide="clock" class="w-3.5 h-3.5 text-[var(--warning)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $proposalsPending }}</div>
        </div>

        <div class="card pt-5 pr-5 pb-5 pl-5">
            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Accepted</span>
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[var(--success)]"></i>
            </div>
            <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $proposalsAccepted }}</div>
        </div>
    </div>

    {{-- ═══ MERCHANT ═══ --}}
    @if ($user->isMerchant())
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="store" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Merchant Stats
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-8">
            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Listings</span>
                    <i data-lucide="list" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $totalListings }}</div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Active Listings</span>
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[var(--success)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $activeListings }}</div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Proposals Received</span>
                    <i data-lucide="inbox" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $proposalsReceived }}</div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Pending Inbox</span>
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-[var(--warning)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $proposalsPendingInbox }}</div>
            </div>
        </div>
    @endif

    {{-- ═══ ADMIN ═══ --}}
    @if ($adminOverview)
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="shield" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                System Overview
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-8">
            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Users</span>
                    <i data-lucide="users" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $adminOverview['total_users'] }}</div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">In Circulation</span>
                    <i data-lucide="wallet" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $adminOverview['total_ecto'] }}</div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Active Listings</span>
                    <i data-lucide="store" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $adminOverview['active_listings'] }}</div>
            </div>

            <div class="card pt-5 pr-5 pb-5 pl-5">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Total Transactions</span>
                    <i data-lucide="receipt" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">{{ $adminOverview['total_transactions'] }}</div>
            </div>
        </div>
    @endif

@endsection