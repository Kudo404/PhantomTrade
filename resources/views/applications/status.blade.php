@extends('layouts.app')

@section('title', 'Application Status — Phantom Trade')

@section('content')

    <div class="max-w-[720px] mt-0 mr-auto mb-0 ml-auto">

        <div class="mb-7">
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Application Status
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Track your role applications and progress.
            </p>
        </div>

        {{-- ═══ Progress toward both roles ═══ --}}
        @if ($user->isCivilian())
            <div class="flex items-center gap-3 mb-3.5">
                <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
                <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                    Your Progress
                </span>
                <div class="grow h-[1px] bg-[var(--border)]"></div>
            </div>

            <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(280px,_1fr))] gap-4 mb-8">

                {{-- Merchant progress --}}
                <div class="card pt-5 pr-5 pb-5 pl-5">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="store" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                            <span class="text-[13px] font-semibold text-[var(--text)]">Merchant Path</span>
                        </div>
                        <span class="text-[12px] text-[var(--text-muted)]">
                            {{ $merchantProgress }} / {{ $merchantThreshold }}
                        </span>
                    </div>
                    <div class="bg-[var(--bg)] rounded-[20px] h-1.5 overflow-hidden mb-3">
                        <div style="--tw-inline-width:{{ min(100, ($merchantProgress / max($merchantThreshold, 1)) * 100) }}%" class="h-full rounded-[20px] bg-[var(--accent)] w-[var(--tw-inline-width)]"></div>
                    </div>
                    @if ($merchantProgress >= $merchantThreshold)
                        <a href="/apply/merchant" class="btn inline-flex w-full">
                            <i data-lucide="send"></i>
                            <span>Apply Now</span>
                        </a>
                    @else
                        <div class="text-[12px] text-[var(--text-muted)]">
                            Complete {{ $merchantThreshold - $merchantProgress }} more trade(s) to unlock.
                        </div>
                    @endif
                </div>

                {{-- Hunter progress --}}
                <div class="card pt-5 pr-5 pb-5 pl-5">
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="target" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                            <span class="text-[13px] font-semibold text-[var(--text)]">Hunter Path</span>
                        </div>
                        <span class="text-[12px] text-[var(--text-muted)]">
                            {{ $hunterProgress }} / {{ $hunterThreshold }}
                        </span>
                    </div>
                    <div class="bg-[var(--bg)] rounded-[20px] h-1.5 overflow-hidden mb-3">
                        <div style="--tw-inline-width:{{ min(100, ($hunterProgress / max($hunterThreshold, 1)) * 100) }}%" class="h-full rounded-[20px] bg-[var(--accent)] w-[var(--tw-inline-width)]"></div>
                    </div>
                    @if ($hunterProgress >= $hunterThreshold)
                        <a href="/apply/hunter" class="btn inline-flex w-full">
                            <i data-lucide="send"></i>
                            <span>Apply Now</span>
                        </a>
                    @else
                        <div class="text-[12px] text-[var(--text-muted)]">
                            Make {{ $hunterThreshold - $hunterProgress }} more purchase(s) to unlock.
                        </div>
                    @endif
                </div>

            </div>
        @endif

        {{-- ═══ Applications history ═══ --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Applications
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        @if ($applications->count())
            <div class="[border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] overflow-hidden">
                @foreach ($applications as $app)
                    <div class="pt-4 pr-5 pb-4 pl-5 [border-bottom:1px_solid_var(--border-subtle)] flex justify-between items-center gap-3 flex-wrap">

                        <div>
                            <div class="text-[13.5px] text-[var(--text)] font-medium">
                                {{ $app->targetRoleLabel() }} Application
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)] mt-0.5">
                                Submitted {{ $app->created_at->diffForHumans() }}
                                @if ($app->reviewed_at)
                                    · Reviewed {{ $app->reviewed_at->diffForHumans() }}
                                @endif
                            </div>
                            @if ($app->isRejected() && $app->rejection_reason)
                                <div class="text-[11.5px] text-[var(--danger)] mt-1">
                                    Reason: {{ $app->rejection_reason }}
                                </div>
                            @endif
                        </div>

                        <span style="--tw-inline-background:{{ $app->statusColor() }}22;--tw-inline-color:{{ $app->statusColor() }}" class="bg-[var(--tw-inline-background)] text-[var(--tw-inline-color)] pt-1 pr-3 pb-1 pl-3 rounded-[20px] text-[11px] font-semibold uppercase tracking-[0.05em]">
                            {{ $app->statusLabel() }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="[border:1px_dashed_var(--border)] rounded-lg bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center">
                <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                    No applications yet
                </div>
                <div class="text-[12.5px] text-[var(--text-muted)]">
                    Complete trades or purchases to unlock a promotion path.
                </div>
            </div>
        @endif

    </div>

@endsection