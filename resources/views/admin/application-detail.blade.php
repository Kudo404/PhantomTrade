@extends('layouts.app')

@section('title', 'Application Review — Phantom Trade')

@section('content')

    <div class="max-w-[800px] mt-0 mr-auto mb-0 ml-auto">

        {{-- Header --}}
        <div class="flex justify-between items-start gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                    {{ $application->targetRoleLabel() }} Application
                </h1>
                <p class="text-[13.5px] text-[var(--text-secondary)]">
                    Submitted {{ $application->created_at->diffForHumans() }}
                </p>
            </div>
            <span style="--tw-inline-background:{{ $application->statusColor() }}22;--tw-inline-color:{{ $application->statusColor() }}" class="bg-[var(--tw-inline-background)] text-[var(--tw-inline-color)] pt-[5px] pr-3.5 pb-[5px] pl-3.5 rounded-[20px] text-[12px] font-semibold uppercase tracking-[0.05em]">
                {{ $application->statusLabel() }}
            </span>
        </div>

        {{-- Applicant card --}}
        <div class="card pt-5 pr-5 pb-5 pl-5 mb-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-[var(--accent)] text-[#fff] flex items-center justify-center font-semibold text-[22px] shrink-0">
                    {{ strtoupper(substr($application->user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-[16px] font-semibold text-[var(--text)]">
                        <x-user-name :user="$application->user" />
                    </div>
                    <div class="text-[12.5px] text-[var(--text-muted)] mt-0.5">
                        {{ $application->user->email }}
                    </div>
                    <div class="text-[11.5px] text-[var(--text-muted)] mt-1">
                        Current role: <strong class="text-[var(--text-secondary)]">{{ $application->user->roleLabel() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form data --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Application Details
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <div class="card pt-5 pr-5 pb-5 pl-5 mb-6">
            @php $data = $application->form_data ?? []; @endphp

            <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(220px,_1fr))] gap-[18px]">
                @if (! empty($data['name']))
                    <div>
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.05em] mb-1">
                            Name
                        </div>
                        <div class="text-[13.5px] text-[var(--text)]">{{ $data['name'] }}</div>
                    </div>
                @endif

                @if (! empty($data['hunter_name']))
                    <div>
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.05em] mb-1">
                            Hunter Name
                        </div>
                        <div class="text-[13.5px] text-[var(--text)]">{{ $data['hunter_name'] }}</div>
                    </div>
                @endif

                @if (! empty($data['contact_email']))
                    <div>
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.05em] mb-1">
                            Contact Email
                        </div>
                        <div class="text-[13.5px] text-[var(--text)]">{{ $data['contact_email'] }}</div>
                    </div>
                @endif

                @if (! empty($data['contact_phone']))
                    <div>
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.05em] mb-1">
                            Contact Phone
                        </div>
                        <div class="text-[13.5px] text-[var(--text)]">{{ $data['contact_phone'] }}</div>
                    </div>
                @endif

                @if (! empty($data['location_name']))
                    <div class="[grid-column:1_/_-1]">
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.05em] mb-1">
                            Location
                        </div>
                        <div class="text-[13.5px] text-[var(--text)]">{{ $data['location_name'] }}</div>
                        @if (! empty($data['latitude']) && ! empty($data['longitude']))
                            <div class="text-[11.5px] text-[var(--text-muted)] mt-0.5">
                                Coordinates: {{ $data['latitude'] }}, {{ $data['longitude'] }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if (! empty($data['reason']))
                <div class="mt-5 pt-5 [border-top:1px_solid_var(--border-subtle)]">
                    <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-[0.05em] mb-1.5">
                        Reason
                    </div>
                    <div class="text-[13.5px] text-[var(--text-secondary)] leading-[1.6] italic">
                        "{{ $data['reason'] }}"
                    </div>
                </div>
            @endif
        </div>

        {{-- Review info (if already reviewed) --}}
        @if (! $application->isPending())
            <div class="card pt-5 pr-5 pb-5 pl-5 mb-6 {{ $application->isApproved() ? 'bg-[#ecfdf5]' : 'bg-[#fef2f2]' }} {{ $application->isApproved() ? '[border-color:#a7f3d0]' : '[border-color:#fecaca]' }}">
                <div class="flex items-start gap-3">
                    <i data-lucide="{{ $application->isApproved() ? 'check-circle-2' : 'x-circle' }}" class="w-[18px] h-[18px] {{ $application->isApproved() ? 'text-[#059669]' : 'text-[#e11d48]' }} shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-[13.5px] font-semibold {{ $application->isApproved() ? 'text-[#059669]' : 'text-[#e11d48]' }}">
                            {{ $application->isApproved() ? 'Approved' : 'Rejected' }}
                        </div>
                        <div class="text-[12.5px] text-[var(--text-secondary)] mt-1">
                            Reviewed by <strong>{{ $application->reviewer?->name ?? 'Unknown' }}</strong>
                            · {{ $application->reviewed_at?->diffForHumans() }}
                        </div>
                        @if ($application->rejection_reason)
                            <div class="text-[12.5px] text-[var(--text-secondary)] mt-1.5">
                                <strong>Reason:</strong> {{ $application->rejection_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        @if ($application->isPending())
            <div class="grid [grid-template-columns:1fr_1fr] gap-4">

                {{-- Approve --}}
                <div class="card pt-5 pr-5 pb-5 pl-5">
                    <div class="text-[13.5px] font-semibold text-[var(--text)] mb-1.5">
                        Approve Application
                    </div>
                    <div class="text-[12.5px] text-[var(--text-muted)] mb-4">
                        Changes the user's role to {{ $application->targetRoleLabel() }} and notifies them.
                    </div>
                    <form method="POST" action="/admin/applications/{{ $application->id }}/approve" class="mt-0 mr-0 mb-0 ml-0">
                        @csrf
                        <button type="submit" class="w-full bg-[#059669] text-[#fff] [border:none] pt-[11px] pr-5 pb-[11px] pl-5 rounded-md font-semibold text-[13.5px] cursor-pointer [font-family:inherit] inline-flex items-center justify-center gap-2"
                               >
                            <i data-lucide="check" class="w-[15px] h-[15px]"></i>
                            Approve
                        </button>
                    </form>
                </div>

                {{-- Reject --}}
                <div class="card pt-5 pr-5 pb-5 pl-5">
                    <div class="text-[13.5px] font-semibold text-[var(--text)] mb-1.5">
                        Reject Application
                    </div>
                    <div class="text-[12.5px] text-[var(--text-muted)] mb-4">
                        Applicant can re-apply after the cooldown period.
                    </div>
                    <form method="POST" action="/admin/applications/{{ $application->id }}/reject" class="mt-0 mr-0 mb-0 ml-0">
                        @csrf
                        <textarea name="rejection_reason" required maxlength="500"
                                  placeholder="Reason for rejection (visible to applicant)..." class="w-full pt-2.5 pr-2.5 pb-2.5 pl-2.5 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13px] [font-family:inherit] outline-none min-h-20 resize-y mb-3"
                                 >{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <div class="text-[var(--danger)] text-[12px] mb-2">{{ $message }}</div> @enderror
                        <button type="submit" class="w-full bg-[#fff] text-[#e11d48] [border:1px_solid_#e11d48] pt-[11px] pr-5 pb-[11px] pl-5 rounded-md font-semibold text-[13.5px] cursor-pointer [font-family:inherit] inline-flex items-center justify-center gap-2"
                               >
                            <i data-lucide="x" class="w-[15px] h-[15px]"></i>
                            Reject
                        </button>
                    </form>
                </div>

            </div>
        @endif

    </div>

@endsection