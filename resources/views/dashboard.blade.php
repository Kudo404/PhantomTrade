@extends('layouts.app')

@section('title', 'Dashboard — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="flex justify-between items-end gap-4 mb-8 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Welcome back, {{ $hunter['name'] }}
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                {{ $hunter['role'] }} · {{ now()->format('l, F j, Y') }}
            </p>
        </div>

        <a href="/marketplace/wanted/create" class="btn">
            <i data-lucide="plus"></i>
            <span>New Wanted Item</span>
        </a>
    </div>

    {{-- ═══ KPI STRIP ═════════════════════════════════ --}}
    @php
        $kpis = [
            [
                'label' => 'Balance',
                'value' => '₱ ' . auth()->user()->ecto,
                'sub'   => 'Available to spend',
                'icon'  => 'wallet',
            ],
            [
                'label' => 'Inventory',
                'value' => \App\Models\Inventory::where('user_id', auth()->id())->sum('quantity'),
                'sub'   => 'Items owned',
                'icon'  => 'package',
            ],
            [
                'label' => 'Wanted Ads',
                'value' => \App\Models\WantedPost::where('user_id', auth()->id())->where('status', 'open')->count(),
                'sub'   => 'Currently open',
                'icon'  => 'tag',
            ],
            [
                'label' => 'Catalog Gear',
                'value' => $totalGear,
                'sub'   => 'Available in shop',
                'icon'  => 'store',
            ],
        ];
    @endphp

    <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(200px,_1fr))] gap-0 [border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden mb-8">
        @foreach ($kpis as $i => $kpi)
            <div class="pt-5 pr-6 pb-5 pl-6 {{ $i < count($kpis) - 1 ? '[border-right:1px_solid_var(--border)]' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                        {{ $kpi['label'] }}
                    </span>
                    <i data-lucide="{{ $kpi['icon'] }}" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[28px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                    {{ $kpi['value'] }}
                </div>
                <div class="text-[12px] text-[var(--text-muted)] mt-1.5">
                    {{ $kpi['sub'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══ ADMIN ANALYTICS ══════════════════════════ --}}
    @if ($admin)
        <div class="flex items-center gap-3 mt-0 mr-0 mb-5 ml-0">
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                System Analytics
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        {{-- Admin KPI row --}}
        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(180px,_1fr))] gap-4 mb-6">
            @php
                $adminKpis = [
                    ['label' => 'Total Users',   'value' => $admin['total_users'],   'icon' => 'users'],
                    ['label' => 'In System','value' => $admin['total_ecto'],    'icon' => 'wallet'],
                    ['label' => 'Active Listings','value'=> $admin['active_listings'],'icon' => 'store'],
                    ['label' => 'Purchases',     'value' => $admin['total_purchases'],'icon' => 'shopping-cart'],
                ];
            @endphp

            @foreach ($adminKpis as $kpi)
                <div class="card pt-5 pr-5 pb-5 pl-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                            {{ $kpi['label'] }}
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-[var(--bg)] flex items-center justify-center">
                            <i data-lucide="{{ $kpi['icon'] }}" class="w-[15px] h-[15px] text-[var(--text-secondary)]"></i>
                        </div>
                    </div>
                    <div class="text-[30px] font-semibold tracking-[-0.02em] text-[var(--text)] leading-[1.1]">
                        {{ $kpi['value'] }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Range filter --}}
        <div class="flex justify-end items-center gap-2.5 mb-4">
            <span class="text-[12px] text-[var(--text-secondary)]">Date range</span>
            <select id="rangeFilter"
                    onchange="window.location.href = '?range=' + this.value;" class="bg-[var(--surface)] text-[var(--text)] [border:1px_solid_var(--border)] rounded-[var(--radius-sm)] pt-[7px] pr-3 pb-[7px] pl-3 text-[13px] cursor-pointer min-w-[150px] [font-family:inherit]"
                   >
                <option value="7"   {{ $admin['range'] === '7'   ? 'selected' : '' }}>Last 7 days</option>
                <option value="30"  {{ $admin['range'] === '30'  ? 'selected' : '' }}>Last 30 days</option>
                <option value="90"  {{ $admin['range'] === '90'  ? 'selected' : '' }}>Last 90 days</option>
                <option value="all" {{ $admin['range'] === 'all' ? 'selected' : '' }}>All time</option>
            </select>
        </div>

        {{-- Chart --}}
        <div class="card pt-6 pr-6 pb-6 pl-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <div class="text-[14px] font-semibold text-[var(--text)]">Purchases Over Time</div>
                    <div class="text-[12px] text-[var(--text-muted)] mt-0.5">{{ $admin['rangeLabel'] }}</div>
                </div>
                <div class="text-[13px] text-[var(--text-secondary)]">
                    {{ array_sum($admin['purchases_chart']) }} total
                </div>
            </div>
            <div class="h-60 relative">
                <canvas id="purchasesChart"></canvas>
            </div>
        </div>

        {{-- Two column: User breakdown + Economy --}}
        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(340px,_1fr))] gap-4 mb-6">

            {{-- User breakdown --}}
            <div class="card pt-6 pr-6 pb-6 pl-6">
                <div class="flex justify-between items-center mb-5">
                    <div class="text-[14px] font-semibold text-[var(--text)]">User Breakdown</div>
                    <span class="text-[12px] text-[var(--text-muted)]">{{ $admin['total_users'] }} total</span>
                </div>

                @php
                    $totalU = max($admin['total_users'], 1);
                    $roles = [
                        ['label' => 'Civilians',     'count' => $admin['civilians'], 'color' => '#7dd3fc'],
                        ['label' => 'Ghost Hunters', 'count' => $admin['hunters'],   'color' => '#34d399'],
                        ['label' => 'Merchants',     'count' => $admin['merchants'], 'color' => '#c084fc'],
                        ['label' => 'Admins',        'count' => $admin['admins'],    'color' => '#fbbf24'],
                    ];
                @endphp

                @foreach ($roles as $r)
                    <div class="mb-3.5">
                        <div class="flex justify-between text-[13px] mb-1.5">
                            <span class="text-[var(--text-secondary)]">{{ $r['label'] }}</span>
                            <span class="text-[var(--text)] font-medium">{{ $r['count'] }}</span>
                        </div>
                        <div class="bg-[var(--bg)] rounded h-1.5 overflow-hidden">
                            <div style="--tw-inline-background:{{ $r['color'] }};--tw-inline-width:{{ ($r['count'] / $totalU) * 100 }}%" class="h-full rounded bg-[var(--tw-inline-background)] w-[var(--tw-inline-width)]"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Economy --}}
            <div class="card pt-6 pr-6 pb-6 pl-6">
                <div class="text-[14px] font-semibold text-[var(--text)] mb-5">Economy Snapshot</div>

                <div class="grid [grid-template-columns:1fr_1fr] gap-3">
                    <div class="pt-3.5 pr-3.5 pb-3.5 pl-3.5 bg-[var(--bg)] rounded-[var(--radius-sm)]">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">In Circulation</div>
                        <div class="text-[20px] font-semibold text-[var(--text)] mt-1">{{ $admin['total_ecto'] }}</div>
                    </div>
                    <div class="pt-3.5 pr-3.5 pb-3.5 pl-3.5 bg-[var(--bg)] rounded-[var(--radius-sm)]">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">Total Spent</div>
                        <div class="text-[20px] font-semibold text-[var(--text)] mt-1">{{ $admin['total_ecto_spent'] }}</div>
                    </div>
                    <div class="pt-3.5 pr-3.5 pb-3.5 pl-3.5 bg-[var(--bg)] rounded-[var(--radius-sm)]">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">Avg / User</div>
                        <div class="text-[20px] font-semibold text-[var(--text)] mt-1">{{ $admin['avg_ecto'] }}</div>
                    </div>
                    <div class="pt-3.5 pr-3.5 pb-3.5 pl-3.5 bg-[var(--bg)] rounded-[var(--radius-sm)]">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">Items Owned</div>
                        <div class="text-[20px] font-semibold text-[var(--text)] mt-1">{{ $admin['total_inventory'] }}</div>
                    </div>
                </div>

                <div class="mt-5 pt-[18px] [border-top:1px_solid_var(--border-subtle)]">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)] mb-3">
                        Trade Proposals
                    </div>
                    <div class="flex gap-6">
                        <div>
                            <div class="text-[20px] font-semibold text-[var(--warning)]">{{ $admin['pending_proposals'] }}</div>
                            <div class="text-[11px] text-[var(--text-muted)] mt-0.5">Pending</div>
                        </div>
                        <div>
                            <div class="text-[20px] font-semibold text-[var(--success)]">{{ $admin['accepted_proposals'] }}</div>
                            <div class="text-[11px] text-[var(--text-muted)] mt-0.5">Accepted</div>
                        </div>
                        <div>
                            <div class="text-[20px] font-semibold text-[var(--danger)]">{{ $admin['rejected_proposals'] }}</div>
                            <div class="text-[11px] text-[var(--text-muted)] mt-0.5">Rejected</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Recent activity --}}
        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(340px,_1fr))] gap-4 mb-8">

            {{-- Newest users --}}
            <div class="card pt-6 pr-6 pb-6 pl-6">
                <div class="text-[14px] font-semibold text-[var(--text)] mb-4">Newest Users</div>
                @foreach ($admin['recent_users'] as $u)
                    <div class="flex justify-between items-center pt-2.5 pr-0 pb-2.5 pl-0 [border-bottom:1px_solid_var(--border-subtle)]">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[13px] shrink-0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-[13px] text-[var(--text)] font-medium whitespace-nowrap overflow-hidden text-ellipsis">{{ $u->name }}</div>
                                <div class="text-[11px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">{{ $u->email }}</div>
                            </div>
                        </div>
                        <span class="text-[10px] uppercase tracking-[0.05em] text-[var(--text-secondary)] bg-[var(--bg)] pt-[3px] pr-2 pb-[3px] pl-2 rounded font-semibold shrink-0">
                            {{ $u->roleLabel() }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Latest purchases --}}
            <div class="card pt-6 pr-6 pb-6 pl-6">
                <div class="text-[14px] font-semibold text-[var(--text)] mb-4">Latest Purchases</div>
                @forelse ($admin['recent_purchases'] as $p)
                    <div class="flex justify-between items-center pt-2.5 pr-0 pb-2.5 pl-0 [border-bottom:1px_solid_var(--border-subtle)]">
                        <div class="min-w-0">
                            <div class="text-[13px] text-[var(--text)]">
                                <strong class="font-semibold">{{ $p->buyer->name ?? 'Unknown' }}</strong>
                                <span class="text-[var(--text-secondary)]"> bought </span>
                                <span class="font-medium">{{ $p->quantity }}× {{ $p->gear->name ?? '—' }}</span>
                            </div>
                            <div class="text-[11px] text-[var(--text-muted)] mt-0.5">
                                {{ $p->source === 'shop' ? 'Gear Shop' : 'Marketplace' }}
                            </div>
                        </div>
                        <div class="text-[13px] text-[var(--success)] font-semibold shrink-0">
                            +{{ $p->quantity * $p->price_per_unit }}
                        </div>
                    </div>
                @empty
                    <p class="text-[var(--text-muted)] text-[13px]">No purchases yet.</p>
                @endforelse
            </div>

        </div>
    @endif

    {{-- ═══ RECENTLY ADDED GEAR ══════════════════════ --}}
    <div class="flex items-center gap-3 mt-0 mr-0 mb-4 ml-0">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Recently Added Gear
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
    </div>

    <div class="[border:1px_solid_var(--border)] rounded-[var(--radius)] bg-[var(--surface)] overflow-hidden">
        {{-- Header row --}}
        <div class="grid [grid-template-columns:2fr_1fr_1fr_1fr] gap-4 pt-2.5 pr-5 pb-2.5 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
            <div>Gear</div>
            <div>Type</div>
            <div>            Cost</div>
            <div>Energy Cost</div>
        </div>

        @forelse ($recentGear as $gear)
            <div class="grid [grid-template-columns:2fr_1fr_1fr_1fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">
                <div class="text-[13.5px] font-medium text-[var(--text)]">
                    {{ $gear->name }}
                </div>
                <div>
                    <span class="type-tag">{{ $gear->type }}</span>
                </div>
                <div class="text-[13px] text-[var(--text-secondary)]">
                    ₱ {{ $gear->ecto_cost }}
                </div>
                <div class="text-[13px] text-[var(--text-secondary)]">
                    <i data-lucide="zap" style="width:14px; height:14px; color:#f59e0b; display:inline-block; vertical-align:middle; margin-right:2px;"></i> {{ $gear->energy_cost }}
                </div>
            </div>
        @empty
            <div class="pt-8 pr-5 pb-8 pl-5 text-center text-[var(--text-muted)] text-[13px]">
                No gear yet. Time to go shopping!
            </div>
        @endforelse
    </div>

    {{-- ═══ CHART.JS ═════════════════════════════════ --}}
    @if ($admin)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('purchasesChart');
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json(array_keys($admin['purchases_chart'])),
                        datasets: [{
                            label: 'Purchases',
                            data: @json(array_values($admin['purchases_chart'])),
                            borderColor: '#0f172a',
                            backgroundColor: 'rgba(15, 23, 42, 0.05)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#0f172a',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#e2e8f0',
                                padding: 10,
                                cornerRadius: 6,
                                displayColors: false,
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: '#f1f5f9' },
                                ticks: { color: '#94a3b8', font: { size: 11 } },
                                border: { display: false }
                            },
                            y: {
                                grid: { color: '#f1f5f9' },
                                ticks: { color: '#94a3b8', font: { size: 11 }, precision: 0 },
                                beginAtZero: true,
                                border: { display: false }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

@endsection 