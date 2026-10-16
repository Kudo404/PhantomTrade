@extends('layouts.app')

@section('title', 'Hunter License — Phantom Trade')

@section('content')

    {{-- Print-specific styles --}}
    <style>
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main { background: #fff !important; }
            .license-card { box-shadow: none !important; border: 2px solid #0f172a !important; }
        }
    </style>

    <div class="max-w-[800px] mt-0 mr-auto mb-0 ml-auto">

        {{-- Header --}}
        <div class="no-print flex justify-between items-end gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                    Hunter License
                </h1>
                <p class="text-[13.5px] text-[var(--text-secondary)]">
                    Your official Ghost Hunter credential.
                </p>
            </div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 pt-[9px] pr-[18px] pb-[9px] pl-[18px] bg-[var(--accent)] text-[#fff] [border:none] rounded-md font-medium text-[13.5px] cursor-pointer [font-family:inherit]"
                   >
                <i data-lucide="printer" class="w-[15px] h-[15px]"></i>
                <span>Print License</span>
            </button>
        </div>

        {{-- ═══ THE LICENSE ═══ --}}
        <div class="license-card bg-[#ffffff] [border:1px_solid_#e2e8f0] rounded-[12px] overflow-hidden [box-shadow:0_12px_32px_rgba(15,23,42,0.12)]">

            {{-- Top banner --}}
            <div class="bg-[linear-gradient(135deg,_#0f172a_0%,_#1e293b_100%)] pt-6 pr-8 pb-6 pl-8 flex justify-between items-center">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-[#fff] text-[#0f172a] rounded-lg flex items-center justify-center font-extrabold text-[18px]">
                        P
                    </div>
                    <div>
                        <div class="text-[#fff] text-[16px] font-bold tracking-[0.5px]">
                            Phantom Trade
                        </div>
                        <div class="text-[#94a3b8] text-[10.5px] uppercase tracking-[0.15em] mt-[1px]">
                            Official License
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[#94a3b8] text-[9.5px] uppercase tracking-[0.15em]">
                        License No.
                    </div>
                    <div class="text-[#fff] text-[14px] font-bold [font-family:monospace] tracking-[0.5px] mt-0.5">
                        {{ $user->license_number }}
                    </div>
                </div>
            </div>

            {{-- Main body --}}
            <div class="pt-9 pr-10 pb-9 pl-10">

                {{-- Title --}}
                <div class="text-center mb-8">
                    <div class="text-[10.5px] text-[#94a3b8] uppercase tracking-[0.2em] mb-1.5">
                        This certifies that
                    </div>
                    <div class="text-[30px] font-bold text-[#0f172a] tracking-[-0.02em] leading-[1.2]">
                        {{ $user->hunter_name ?? $user->name }}
                    </div>
                    <div class="text-[13px] text-[#64748b] mt-1.5">
                        ({{ $user->name }})
                    </div>
                    <div class="text-[10.5px] text-[#94a3b8] uppercase tracking-[0.2em] mt-5">
                        is a licensed
                    </div>
                    <div class="text-[20px] font-semibold text-[#0f172a] mt-1.5 tracking-[0.05em]">
                        GHOST HUNTER
                    </div>
                </div>

                {{-- Details grid --}}
                <div class="grid [grid-template-columns:repeat(3,_1fr)] gap-6 pt-6 pr-0 pb-6 pl-0 [border-top:1px_solid_#e2e8f0] [border-bottom:1px_solid_#e2e8f0]">

                    {{-- Tier --}}
                    <div>
                        <div class="text-[10px] text-[#94a3b8] uppercase tracking-[0.15em] mb-1.5">
                            Rank / Tier
                        </div>
                        <div class="text-[20px] font-bold text-[#0f172a] tracking-[-0.01em]">
                            {{ $user->hunterTierLabel() }}
                        </div>
                    </div>

                    {{-- Issue date --}}
                    <div>
                        <div class="text-[10px] text-[#94a3b8] uppercase tracking-[0.15em] mb-1.5">
                            Issued On
                        </div>
                        <div class="text-[15px] font-semibold text-[#0f172a]">
                            @if ($user->license_issued_at)
                                {{ \Carbon\Carbon::parse($user->license_issued_at)->format('F j, Y') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <div class="text-[10px] text-[#94a3b8] uppercase tracking-[0.15em] mb-1.5">
                            Base Location
                        </div>
                        <div class="text-[15px] font-semibold text-[#0f172a]">
                            {{ $user->location_name ?: '—' }}
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-between items-end mt-8 gap-6">

                    {{-- Approved by --}}
                    <div>
                        <div class="text-[10px] text-[#94a3b8] uppercase tracking-[0.15em] mb-2">
                            Authorized By
                        </div>
                        @php
                            $approver = \App\Models\RoleApplication::where('user_id', $user->id)
                                ->where('target_role', 'ghost_hunter')
                                ->where('status', 'approved')
                                ->with('reviewer')
                                ->first();
                        @endphp
                        <div class="text-[13px] font-semibold text-[#0f172a]">
                            {{ $approver?->reviewer?->name ?? 'Administrator' }}
                        </div>
                        <div class="text-[11px] text-[#64748b] mt-0.5">
                            Phantom Trade Administration
                        </div>
                    </div>

                    {{-- Seal --}}
                    <div class="w-20 h-20 [border:3px_solid_#0f172a] rounded-full flex flex-col items-center justify-center text-center">
                        <div class="text-[9px] text-[#0f172a] font-bold uppercase tracking-[0.1em] leading-[1.2]">
                            Phantom<br>Trade
                        </div>
                        <div class="text-[7px] text-[#64748b] mt-0.5">
                            OFFICIAL
                        </div>
                    </div>
                </div>

                {{-- Footer note --}}
                <div class="text-center mt-8 pt-5 [border-top:1px_dashed_#e2e8f0]">
                    <div class="text-[10px] text-[#94a3b8] tracking-[0.1em] uppercase">
                        This license is property of Phantom Trade and must be presented upon request.
                    </div>
                </div>
            </div>

        </div>

        {{-- Info notice --}}
        <div class="no-print mt-5 pt-3.5 pr-[18px] pb-3.5 pl-[18px] bg-[#f8fafc] [border:1px_solid_#e2e8f0] rounded-lg text-[12.5px] text-[#64748b] flex gap-2.5 items-start">
            <i data-lucide="info" class="w-[15px] h-[15px] shrink-0 mt-[1px] text-[#94a3b8]"></i>
            <div>
                <strong class="text-[#0f172a] font-semibold">Tip:</strong> Click <strong>Print License</strong> to save it as a PDF or print a physical copy.
            </div>
        </div>

    </div>

@endsection