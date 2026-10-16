@extends('layouts.app')

@section('title', 'Admin Settings — Phantom Trade')

@section('content')

    <div class="max-w-[900px] mt-0 mr-auto mb-0 ml-auto">

        {{-- Page header --}}
        <div class="mb-7">
            <h1 class="text-[24px] font-semibold tracking-[-0.02em] text-[var(--text)] mb-1">
                Admin Settings
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Control promotion thresholds, achievement rules, and application behavior.
            </p>
        </div>


        {{-- ═══ Section: Promotion Thresholds ═══ --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="sliders" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Promotion Thresholds
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <form method="POST" action="/admin/settings" class="card pt-6 pr-6 pb-6 pl-6 mb-8">
            @csrf

            <div class="grid [grid-template-columns:repeat(auto-fit,_minmax(220px,_1fr))] gap-5 mb-5">

                {{-- Merchant threshold --}}
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Merchant: Trades Required
                    </label>
                    <input type="number" name="merchant_trade_threshold"
                           value="{{ old('merchant_trade_threshold', $settings['merchant_trade_threshold']) }}"
                           min="1" max="100" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[14px] [font-family:inherit] outline-none"
                          >
                    <div class="text-[11px] text-[var(--text-muted)] mt-1">
                        Completed trades needed to unlock the merchant application.
                    </div>
                    @error('merchant_trade_threshold') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Hunter threshold --}}
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Hunter: Purchases Required
                    </label>
                    <input type="number" name="hunter_purchase_threshold"
                           value="{{ old('hunter_purchase_threshold', $settings['hunter_purchase_threshold']) }}"
                           min="1" max="100" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[14px] [font-family:inherit] outline-none"
                          >
                    <div class="text-[11px] text-[var(--text-muted)] mt-1">
                        Purchases needed to unlock the hunter application.
                    </div>
                    @error('hunter_purchase_threshold') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Cooldown --}}
                <div>
                    <label class="block text-[12px] font-semibold text-[var(--text-secondary)] mb-1.5 uppercase tracking-[0.05em]">
                        Rejection Cooldown (hours)
                    </label>
                    <input type="number" name="application_cooldown_hours"
                           value="{{ old('application_cooldown_hours', $settings['application_cooldown_hours']) }}"
                           min="0" max="720" required class="w-full pt-2.5 pr-3 pb-2.5 pl-3 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[14px] [font-family:inherit] outline-none"
                          >
                    <div class="text-[11px] text-[var(--text-muted)] mt-1">
                        Wait time before a rejected applicant can re-apply.
                    </div>
                    @error('application_cooldown_hours') <div class="text-[var(--danger)] text-[12px] mt-1">{{ $message }}</div> @enderror
                </div>

            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn">
                    <i data-lucide="save"></i>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>

        {{-- ═══ Section: Achievements ═══ --}}
        <div class="flex items-center gap-3 mb-3.5">
            <i data-lucide="trophy" class="w-3.5 h-3.5 text-[var(--text-muted)]"></i>
            <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
                Achievements
            </span>
            <div class="grow h-[1px] bg-[var(--border)]"></div>
        </div>

        <div class="card pt-0 pr-0 pb-0 pl-0 overflow-hidden">
            {{-- Header row --}}
            <div class="grid [grid-template-columns:2.5fr_1fr_1fr_1fr] gap-4 pt-3 pr-5 pb-3 pl-5 bg-[var(--bg)] [border-bottom:1px_solid_var(--border-subtle)] text-[11px] font-semibold uppercase tracking-[0.06em] text-[var(--text-muted)]">
                <div>Achievement</div>
                <div>Type</div>
                <div>Threshold</div>
                <div class="text-right">Status</div>
            </div>

            @foreach ($achievements as $a)
                <div class="grid [grid-template-columns:2.5fr_1fr_1fr_1fr] gap-4 pt-3.5 pr-5 pb-3.5 pl-5 [border-bottom:1px_solid_var(--border-subtle)] items-center">

                    {{-- Name + description --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-[var(--bg)] flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $a->icon }}" class="w-4 h-4 text-[var(--text-secondary)]"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-medium text-[var(--text)]">
                                {{ $a->name }}
                            </div>
                            <div class="text-[11.5px] text-[var(--text-muted)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $a->description }}
                            </div>
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <span class="type-tag">{{ $a->type }}</span>
                    </div>

                    {{-- Threshold --}}
                    <form method="POST" action="/admin/achievements/{{ $a->id }}/threshold" class="mt-0 mr-0 mb-0 ml-0">
                        @csrf
                        <div class="flex gap-1.5 items-center">
                            <input type="number" name="threshold" value="{{ $a->threshold }}" min="1" max="9999" class="w-[70px] pt-1.5 pr-2 pb-1.5 pl-2 [border:1px_solid_var(--border)] rounded-md bg-[var(--surface)] text-[var(--text)] text-[13px] text-center outline-none"
                                  >
                            <button type="submit" class="bg-[var(--surface)] [border:1px_solid_var(--border)] text-[var(--text-secondary)] pt-1.5 pr-2.5 pb-1.5 pl-2.5 rounded-md text-[11px] font-semibold cursor-pointer [font-family:inherit]"
                                   >
                                Save
                            </button>
                        </div>
                    </form>

                    {{-- Toggle --}}
                    <div class="flex justify-end">
                        <form method="POST" action="/admin/achievements/{{ $a->id }}/toggle" class="mt-0 mr-0 mb-0 ml-0">
                            @csrf
                            <button type="submit" class="{{ $a->is_active ? 'bg-[#ecfdf5]' : 'bg-[var(--bg)]' }} {{ $a->is_active ? 'text-[#059669]' : 'text-[var(--text-muted)]' }} {{ $a->is_active ? '[border:1px_solid_#a7f3d0]' : '[border:1px_solid_var(--border)]' }} pt-[5px] pr-3 pb-[5px] pl-3 rounded-[20px] text-[11px] font-semibold cursor-pointer [font-family:inherit] uppercase tracking-[0.05em]"
                                   >
                                {{ $a->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

@endsection