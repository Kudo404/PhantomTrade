@extends('layouts.app')

@section('title', 'Manage Users — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══ --}}
    <div class="flex justify-between items-end gap-4 mb-6 flex-wrap">
        <div>
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Manage Users
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                View, search, and manage all users in the system.
            </p>
        </div>

        <form method="GET" action="/admin/users" class="relative">
            <i data-lucide="search" class="absolute left-[12px] top-[50%] [transform:translateY(-50%)] w-3.5 h-3.5 text-[var(--text-muted)] pointer-events-none"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Search name or email…" class="pt-2 pr-3 pb-2 pl-[34px] [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13.5px] outline-none w-[260px] [font-family:inherit]"
                  >
        </form>
    </div>

    {{-- ═══ ROLE FILTER CHIPS ═══ --}}
    <div class="flex gap-2 mb-5 flex-wrap">
        @php
            $chips = [
                null            => ['label' => 'All',          'count' => $counts['all']],
                'civilian'      => ['label' => 'Civilians',    'count' => $counts['civilian']],
                'ghost_hunter'  => ['label' => 'Ghost Hunters','count' => $counts['ghost_hunter']],
                'merchant'      => ['label' => 'Merchants',    'count' => $counts['merchant']],
                'admin'         => ['label' => 'Admins',       'count' => $counts['admin']],
            ];
        @endphp

        @foreach ($chips as $value => $chip)
            @php
                $active = $role === $value || ($value === null && ! $role);
                $url = '/admin/users' . ($value ? '?role=' . $value : '') . ($search ? ($value ? '&' : '?') . 'q=' . urlencode($search) : '');
            @endphp
            <a href="{{ $url }}" class="inline-flex items-center gap-1.5 pt-1.5 pr-3.5 pb-1.5 pl-3.5 text-[12.5px] font-medium rounded-[20px] no-underline {{ $active ? '[border:1px_solid_var(--accent)]' : '[border:1px_solid_var(--border)]' }} {{ $active ? 'bg-[var(--accent)]' : 'bg-[var(--surface)]' }} {{ $active ? 'text-[#fff]' : 'text-[var(--text-secondary)]' }}"
              >
                {{ $chip['label'] }}
                <span class="{{ $active ? 'bg-[rgba(255,255,255,0.25)]' : 'bg-[var(--bg)]' }} pt-[1px] pr-[7px] pb-[1px] pl-[7px] rounded-[10px] text-[10.5px]">
                    {{ $chip['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ═══ USER TABLE ═══ --}}
    @if ($users->count())
        <div class="[border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] overflow-hidden">

            {{-- Header row --}}
            <div class="grid [grid-template-columns:2.5fr_2fr_1fr_1fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>User</div>
                <div>Email</div>
                <div>Role</div>
                <div class="text-right">Actions</div>
            </div>

            @foreach ($users as $u)
                <a href="/admin/users/{{ $u->id }}"
                  
                  
                   class="grid [grid-template-columns:2.5fr_2fr_1fr_1fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center no-underline [transition:background_0.12s]  hover:bg-[var(--bg)]">

                    {{-- User --}}
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[12px] shrink-0">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                <x-user-name :user="$u" />
                            </div>
                            @if ($u->hunter_name && $u->hunter_name !== $u->name)
                                <div class="text-[11px] text-[var(--text-muted)]">
                                    aka {{ $u->hunter_name }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="text-[12.5px] text-[var(--text-secondary)] whitespace-nowrap overflow-hidden text-ellipsis">
                        {{ $u->email }}
                    </div>

                    {{-- Role --}}
                    <div>
                        <span class="type-tag">{{ $u->roleLabel() }}</span>
                    </div>

                    {{-- Actions --}}
                    <div class="text-right text-[12px] text-[var(--text-muted)]">
                        View →
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $users->links() }}
        </div>
    @else
        <div class="[border:1px_dashed_var(--border)] rounded-lg bg-[var(--surface)] pt-14 pr-6 pb-14 pl-6 text-center">
            <div class="w-[52px] h-[52px] mt-0 mr-auto mb-4 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                <i data-lucide="users" class="w-[22px] h-[22px] text-[var(--text-muted)]"></i>
            </div>
            <div class="text-[15px] font-semibold text-[var(--text)] mb-1.5">
                No users found
            </div>
            <div class="text-[13px] text-[var(--text-muted)]">
                @if ($search)
                    Try a different search term.
                @else
                    Nothing to show here.
                @endif
            </div>
        </div>
    @endif

@endsection