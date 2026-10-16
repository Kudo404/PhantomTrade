@extends('layouts.app')

@section('title', 'Achievements — Phantom Trade')

@section('content')

    <div class="max-w-[900px] mt-0 mr-auto mb-0 ml-auto">

        <div class="mb-7">
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Achievements
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Track your progress toward becoming a Certified Merchant or Licensed Hunter.
            </p>
        </div>

        {{-- Summary strip --}}
        <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(150px,_1fr))] gap-0 [border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] overflow-hidden mb-7">
            <div class="pt-[18px] pr-[22px] pb-[18px] pl-[22px] [border-right:1px_solid_var(--border)]">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Unlocked
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] mt-1.5">
                    {{ $totalUnlocked }} <span class="text-[var(--text-muted)] text-[16px]">/ {{ $totalActive }}</span>
                </div>
            </div>

            <div class="pt-[18px] pr-[22px] pb-[18px] pl-[22px] [border-right:1px_solid_var(--border)]">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    In Progress
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] mt-1.5">
                    {{ $inProgress }}
                </div>
            </div>

            <div class="pt-[18px] pr-[22px] pb-[18px] pl-[22px]">
                <div class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Locked
                </div>
                <div class="text-[26px] font-semibold tracking-[-0.02em] text-[var(--text)] mt-1.5">
                    {{ $locked }}
                </div>
            </div>
        </div>

        {{-- Section header --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="trophy" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                All Achievements
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        @if ($items->count())
            <div class="[border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] overflow-hidden">
                @foreach ($items as $item)
                    @php
                        $a = $item['achievement'];
                        $unlocked = $item['unlocked'];
                        $percent = $item['percent'];
                        $current = $item['current'];
                        $target  = $item['target'];
                    @endphp

                    <div class="pt-[18px] pr-[22px] pb-[18px] pl-[22px] [border-bottom:1px_solid_var(--border-subtle)] flex items-center gap-4 {{ $unlocked ? 'bg-[rgba(5,150,105,0.03)]' : '' }}">

                        {{-- Icon --}}
                        <div class="w-11 h-11 rounded-[10px] shrink-0 {{ $unlocked ? 'bg-[#ecfdf5]' : 'bg-[var(--bg)]' }} {{ $unlocked ? '[border:1px_solid_#a7f3d0]' : '[border:1px_solid_var(--border)]' }} flex items-center justify-center">
                            <i data-lucide="{{ $a->icon }}" class="w-5 h-5 {{ $unlocked ? 'text-[#059669]' : 'text-[var(--text-secondary)]' }}"
                              ></i>
                        </div>

                        {{-- Content --}}
                        <div class="grow min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <div class="text-[14px] font-semibold text-[var(--text)]">
                                    {{ $a->name }}
                                </div>
                                @if ($unlocked)
                                    <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold uppercase tracking-[0.05em] text-[#059669]">
                                        <i data-lucide="check" class="w-[11px] h-[11px]"></i>
                                        Unlocked
                                    </span>
                                @endif
                            </div>

                            <div class="text-[12.5px] text-[var(--text-secondary)] {{ $unlocked ? 'mb-1' : 'mb-2.5' }}">
                                {{ $a->description }}
                            </div>

                            @if (! $unlocked)
                                <div class="flex items-center gap-2.5">
                                    <div class="grow bg-[var(--bg)] rounded-[20px] h-1.5 overflow-hidden">
                                        <div style="--tw-inline-width:{{ $percent }}%" class="h-full rounded-[20px] bg-[var(--accent)] w-[var(--tw-inline-width)]"></div>
                                    </div>
                                    <span class="text-[11.5px] text-[var(--text-muted)] whitespace-nowrap font-medium">
                                        {{ $current }} / {{ $target }}
                                    </span>
                                </div>
                            @else
                                @if ($item['unlocked_at'])
                                    <div class="text-[11.5px] text-[var(--text-muted)]">
                                        Unlocked {{ $item['unlocked_at']->diffForHumans() }}
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- Status icon --}}
                        <div class="shrink-0">
                            @if ($unlocked)
                                <div class="w-9 h-9 rounded-full bg-[#ecfdf5] flex items-center justify-center">
                                    <i data-lucide="check" class="w-[18px] h-[18px] text-[#059669]"></i>
                                </div>
                            @elseif ($percent > 0)
                                <div class="w-9 h-9 rounded-full bg-[var(--bg)] [border:1px_solid_var(--border)] flex items-center justify-center">
                                    <i data-lucide="trending-up" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full bg-[var(--bg)] flex items-center justify-center">
                                    <i data-lucide="lock" class="w-[15px] h-[15px] text-[var(--text-muted)]"></i>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="[border:1px_dashed_var(--border)] rounded-lg bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center">
                <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                    <i data-lucide="trophy" class="w-5 h-5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                    No achievements yet
                </div>
                <div class="text-[12.5px] text-[var(--text-muted)]">
                    Achievements will appear here once configured by an admin.
                </div>
            </div>
        @endif

    </div>

@endsection