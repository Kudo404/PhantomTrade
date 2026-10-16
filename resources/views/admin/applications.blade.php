@extends('layouts.app')

@section('title', 'Applications — Phantom Trade')

@section('content')

    <div class="max-w-[1000px] mt-0 mr-auto mb-0 ml-auto">

        {{-- Header --}}
        <div class="flex justify-between items-end gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                    Role Applications
                </h1>
                <p class="text-[13.5px] text-[var(--text-secondary)]">
                    Review and approve civilian promotion requests.
                </p>
            </div>
            <a href="/admin/settings" class="btn btn-secondary">
                <i data-lucide="settings"></i>
                <span>Settings</span>
            </a>
        </div>

        {{-- Status tabs --}}
        <div class="flex gap-2 mb-5 flex-wrap">
            @php
                $tabs = [
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'all'      => 'All',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                @php $active = $status === $key; @endphp
                <a href="/admin/applications?status={{ $key }}" class="inline-flex items-center gap-1.5 pt-1.5 pr-3.5 pb-1.5 pl-3.5 text-[12.5px] font-medium rounded-[20px] no-underline {{ $active ? '[border:1px_solid_var(--accent)]' : '[border:1px_solid_var(--border)]' }} {{ $active ? 'bg-[var(--accent)]' : 'bg-[var(--surface)]' }} {{ $active ? 'text-[#fff]' : 'text-[var(--text-secondary)]' }}"
                  >
                    {{ $label }}
                    <span class="{{ $active ? 'bg-[rgba(255,255,255,0.25)]' : 'bg-[var(--bg)]' }} pt-[1px] pr-[7px] pb-[1px] pl-[7px] rounded-[10px] text-[10.5px]">
                        {{ $counts[$key] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Applications table --}}
        @if ($applications->count())
            <div class="[border:1px_solid_var(--border)] rounded-lg bg-[var(--surface)] overflow-hidden">
                {{-- Header row --}}
                <div class="grid [grid-template-columns:2fr_1fr_1fr_1fr_1fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                    <div>Applicant</div>
                    <div>Target Role</div>
                    <div>Submitted</div>
                    <div>Status</div>
                    <div class="text-right">Action</div>
                </div>

                @foreach ($applications as $app)
                    <div class="grid [grid-template-columns:2fr_1fr_1fr_1fr_1fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                        {{-- Applicant --}}
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[12px] shrink-0">
                                {{ strtoupper(substr($app->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-[13px] font-medium text-[var(--text)] whitespace-nowrap overflow-hidden text-ellipsis">
                                    <x-user-name :user="$app->user" />
                                </div>
                                <div class="text-[11px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                    {{ $app->user->email }}
                                </div>
                            </div>
                        </div>

                        {{-- Target role --}}
                        <div>
                            <span class="type-tag">{{ $app->targetRoleLabel() }}</span>
                        </div>

                        {{-- Submitted --}}
                        <div class="text-[12.5px] text-[var(--text-secondary)]">
                            {{ $app->created_at->diffForHumans() }}
                        </div>

                        {{-- Status --}}
                        <div>
                            <span style="--tw-inline-background:{{ $app->statusColor() }}22;--tw-inline-color:{{ $app->statusColor() }}" class="bg-[var(--tw-inline-background)] text-[var(--tw-inline-color)] pt-[3px] pr-2.5 pb-[3px] pl-2.5 rounded-[20px] text-[11px] font-semibold uppercase tracking-[0.05em]">
                                {{ $app->statusLabel() }}
                            </span>
                        </div>

                        {{-- Action --}}
                        <div class="text-right">
                            <a href="/admin/applications/{{ $app->id }}" class="inline-flex items-center gap-[5px] text-[12.5px] text-[var(--accent)] font-medium no-underline"
                              >
                                Review
                                <i data-lucide="arrow-right" class="w-[13px] h-[13px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="[border:1px_dashed_var(--border)] rounded-lg bg-[var(--surface)] pt-12 pr-6 pb-12 pl-6 text-center">
                <div class="w-11 h-11 mt-0 mr-auto mb-3 ml-auto rounded-full bg-[var(--bg)] flex items-center justify-center">
                    <i data-lucide="inbox" class="w-5 h-5 text-[var(--text-muted)]"></i>
                </div>
                <div class="text-[14px] font-semibold text-[var(--text)] mb-1">
                    No {{ $status === 'all' ? '' : $status }} applications
                </div>
                <div class="text-[12.5px] text-[var(--text-muted)]">
                    @if ($status === 'pending')
                        You're all caught up.
                    @else
                        Nothing to show here.
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection