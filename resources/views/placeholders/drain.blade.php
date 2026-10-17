@extends('layouts.app')

@section('title', 'Spectral Drain Chamber — Phantom Trade')

@section('content')

    {{-- ═══ PAGE HEADER ═══════════════════════════════ --}}
    <div class="flex justify-between items-end gap-4 mb-7 flex-wrap">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[rgba(245,158,11,0.1)] text-[#d97706] dark:text-[#fbbf24] text-[11px] font-semibold uppercase tracking-[0.08em] mb-2">
                <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                Scenario 3: Field Haunt & Life-Force Drain Simulator
            </div>
            <h1 class="text-[26px] font-bold tracking-[-0.03em] text-[var(--text)] mb-1">
                Spectral Drain Chamber
            </h1>
            <p class="text-[13.5px] text-[var(--text-secondary)]">
                Deploy your calibrated gear rig into controlled spectral chambers to test spirit ward dissipation, soul-anchor integrity, and capture resonance.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="/loadout" class="btn btn-secondary flex items-center gap-2">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>Configure Rig in Customizer</span>
            </a>
        </div>
    </div>

    {{-- ═══ SIMULATION OUTCOME ALERT (IF JUST RUN) ═════ --}}
    @if (session('simulation_result'))
        @php $res = session('simulation_result'); @endphp
        <div class="mb-8 p-5 rounded-xl border {{ $res['captured'] ? 'border-[#10b981] bg-[rgba(16,185,129,0.05)]' : 'border-[#e11d48] bg-[rgba(225,29,72,0.05)]' }} shadow-sm">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white {{ $res['captured'] ? 'bg-[#10b981]' : 'bg-[#e11d48]' }}">
                        <i data-lucide="{{ $res['captured'] ? 'check-circle-2' : 'alert-octagon' }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider {{ $res['captured'] ? 'text-[#059669]' : 'text-[#dc2626]' }}">
                            Simulation Telemetry: {{ $res['captured'] ? 'SUCCESSFUL CONTAINMENT' : 'CONTAINMENT FAILED' }}
                        </div>
                        <h3 class="text-[18px] font-bold text-[var(--text)]">
                            Target: {{ $res['entity_name'] }}
                        </h3>
                    </div>
                </div>

                @if ($res['captured'])
                    <div class="text-right">
                        <span class="text-[11px] uppercase tracking-wider text-[var(--text-muted)] block">Ectoplasm Harvested</span>
                        <span class="text-[22px] font-bold text-[#059669]">+₱ {{ $res['ecto_earned'] }} Ecto</span>
                    </div>
                @endif
            </div>

            {{-- Metric Breakdown Strip --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 pt-4 border-t border-[var(--border-subtle)] text-[12px]">
                <div>
                    <span class="text-[var(--text-muted)] block text-[10.5px]">Base Spectral Drain</span>
                    <span class="font-bold text-[var(--text)]">{{ $res['base_drain'] }} EP</span>
                </div>
                <div>
                    <span class="text-[var(--text-muted)] block text-[10.5px]">Spirit Ward Absorption</span>
                    <span class="font-bold text-[#059669]">-{{ $res['ward_absorbed'] }} EP Dissipated</span>
                </div>
                <div>
                    <span class="text-[var(--text-muted)] block text-[10.5px]">Soul-Anchor Grounding</span>
                    <span class="font-bold {{ $res['anchor_held'] ? 'text-[#6366f1]' : 'text-[#e11d48]' }}">
                        {{ $res['anchor_held'] ? '✅ Tether Grounded' : '❌ Tether Destabilized (+15 Drain)' }}
                    </span>
                </div>
                <div>
                    <span class="text-[var(--text-muted)] block text-[10.5px]">Frequency Resonance</span>
                    <span class="font-bold {{ $res['frequency_matched'] ? 'text-[#9333ea]' : 'text-[var(--text-muted)]' }}">
                        {{ $res['frequency_matched'] ? '✅ Resonance Locked' : '❌ Frequency Mismatch' }}
                    </span>
                </div>
                <div>
                    <span class="text-[var(--text-muted)] block text-[10.5px]">Energy Consumed</span>
                    <span class="font-bold text-[#d97706]">-{{ $res['energy_spent'] }} EP</span>
                </div>
                <div>
                    <span class="text-[var(--text-muted)] block text-[10.5px]">Life-Force Lost</span>
                    <span class="font-bold text-[#8b5cf6]">-{{ $res['life_force_lost'] }} LP</span>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══ ACTIVE RIG TELEMETRY PREVIEW ══════════════ --}}
    <div class="p-5 border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] mb-8 shadow-sm">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[var(--accent)] text-white flex items-center justify-center">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-[14px] font-bold text-[var(--text)]">Active Rig Diagnostic</h3>
                    <p class="text-[11.5px] text-[var(--text-secondary)]">Telemetry values loaded from your equipped gear and tuning profiles.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[12px] text-[var(--text-secondary)]">Equipped Slots:</span>
                <span class="px-2 py-0.5 rounded bg-[var(--bg)] border border-[var(--border-subtle)] font-bold text-[12px] text-[var(--text)]">
                    {{ $telemetry['equipped_count'] }} / 4
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
            <div class="p-3 rounded-lg bg-[var(--bg)] border border-[var(--border-subtle)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-1">Ward Barrier</span>
                <span class="text-[18px] font-bold text-[#059669]">{{ $telemetry['ward_absorption'] }}%</span>
                <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">{{ $telemetry['has_ward'] ? 'Shield Online' : 'No Ward' }}</span>
            </div>

            <div class="p-3 rounded-lg bg-[var(--bg)] border border-[var(--border-subtle)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-1">Soul Stability</span>
                <span class="text-[18px] font-bold {{ $telemetry['stability'] < 50 ? 'text-[#e11d48]' : 'text-[#6366f1]' }}">{{ $telemetry['stability'] }}%</span>
                <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">{{ $telemetry['has_anchor'] ? 'Anchor Active' : 'Unanchored' }}</span>
            </div>

            <div class="p-3 rounded-lg bg-[var(--bg)] border border-[var(--border-subtle)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-1">Rig Drain Rate</span>
                <span class="text-[18px] font-bold text-[#f59e0b]">{{ $telemetry['total_drain'] }} EP</span>
                <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">Energy / Hunt</span>
            </div>

            <div class="p-3 rounded-lg bg-[var(--bg)] border border-[var(--border-subtle)]">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] block mb-1">Containment Trap</span>
                <span class="text-[18px] font-bold {{ $telemetry['has_trap'] ? 'text-[var(--text)]' : 'text-[var(--text-muted)]' }}">
                    {{ $telemetry['has_trap'] ? 'Armored Trap' : 'None' }}
                </span>
                <span class="text-[11px] text-[var(--text-secondary)] block mt-0.5">{{ $telemetry['has_trap'] ? 'Capture Ready' : 'Trap Required' }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ PERSISTENT SPECTRAL RESERVES ═══════════════ --}}
    <div class="p-5 border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] mb-8 shadow-sm">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
            <div>
                <h3 class="text-[15px] font-bold text-[var(--text)]">Spectral Reserves</h3>
                <p class="text-[12px] text-[var(--text-secondary)] mt-1">Every encounter consumes rig energy and life-force. Both reserves persist between visits.</p>
            </div>
            @if ($user->energy < \App\Models\User::MAX_ENERGY || $user->life_force < \App\Models\User::MAX_LIFE_FORCE)
                <form action="/drain/recover" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary text-[12px] py-2">
                        <i data-lucide="battery-charging" class="w-4 h-4 inline-block mr-1"></i>
                        Recharge — {{ \App\Models\User::RECOVERY_ECTO_COST }} Ecto
                    </button>
                </form>
            @else
                <span class="px-3 py-2 rounded-lg text-[12px] font-semibold text-[#059669] bg-[rgba(16,185,129,0.08)]">Reserves fully charged</span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[12px] font-semibold text-[var(--text-secondary)]">Rig Energy</span>
                    <span class="text-[13px] font-bold text-[#d97706]">{{ $user->energy }} / {{ \App\Models\User::MAX_ENERGY }} EP</span>
                </div>
                <div class="h-2.5 rounded-full overflow-hidden bg-[var(--bg)] border border-[var(--border-subtle)]">
                    <div class="h-full rounded-full bg-[#f59e0b]" style="width: {{ min(100, max(0, $user->energy)) }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[12px] font-semibold text-[var(--text-secondary)]">Life-Force</span>
                    <span class="text-[13px] font-bold text-[#8b5cf6]">{{ $user->life_force }} / {{ \App\Models\User::MAX_LIFE_FORCE }} LP</span>
                </div>
                <div class="h-2.5 rounded-full overflow-hidden bg-[var(--bg)] border border-[var(--border-subtle)]">
                    <div class="h-full rounded-full bg-[#8b5cf6]" style="width: {{ min(100, max(0, $user->life_force)) }}%"></div>
                </div>
            </div>
        </div>
        @if ($user->energy <= 0 || $user->life_force <= 0)
            <p class="mt-4 text-[12px] font-semibold text-[#e11d48]">Hunt lockout active: recharge your spectral reserves before attempting another encounter.</p>
        @else
            <p class="mt-4 text-[11px] text-[var(--text-muted)]">Recovery restores both reserves to 100 for {{ \App\Models\User::RECOVERY_ECTO_COST }} Ecto. Encounters are rejected if either reserve cannot cover the full cost.</p>
        @endif
        @if ($errors->has('encounter') || $errors->has('recovery'))
            <div class="mt-4 p-3 rounded-lg border border-[#e11d48] bg-[rgba(225,29,72,0.06)] text-[12px] text-[#e11d48]">
                {{ $errors->first('encounter') ?: $errors->first('recovery') }}
            </div>
        @endif
        @if (session('success'))
            <div class="mt-4 p-3 rounded-lg border border-[#10b981] bg-[rgba(16,185,129,0.06)] text-[12px] text-[#059669]">
                {{ session('success') }}
            </div>
        @endif
        @if (session('info'))
            <div class="mt-4 p-3 rounded-lg border border-[var(--border)] text-[12px] text-[var(--text-secondary)]">
                {{ session('info') }}
            </div>
        @endif
    </div>

    {{-- ═══ SPECTRAL ENCOUNTER SIMULATION PODS ═════════ --}}
    <div class="flex items-center gap-3 mb-4">
        <span class="text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">
            Select Spectral Chamber to Engage
        </span>
        <div class="grow h-[1px] bg-[var(--border)]"></div>
        <span class="text-[12px] text-[var(--text-muted)]">Harvest Ecto on successful containment</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        @foreach ($entities as $ent)
            <div class="border border-[var(--border)] rounded-[var(--radius)] bg-[var(--surface)] p-5 flex flex-col justify-between hover:shadow-lg transition-all relative overflow-hidden group">

                <div>
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[var(--bg)] border border-[var(--border-subtle)] flex items-center justify-center text-[var(--accent)] group-hover:scale-110 transition-transform">
                            <i data-lucide="{{ $ent['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded
                            {{ $ent['danger'] === 'Extreme' ? 'bg-[#fee2e2] text-[#b91c1c]' :
                               ($ent['danger'] === 'Severe' ? 'bg-[#ffedd5] text-[#c2410c]' :
                               ($ent['danger'] === 'High' ? 'bg-[#fef3c7] text-[#b45309]' : 'bg-[#f3f4f6] text-[var(--text)]')) }}">
                            {{ $ent['class'] }} • {{ $ent['danger'] }}
                        </span>
                    </div>

                    <h4 class="text-[15px] font-bold text-[var(--text)] mb-1">
                        {{ $ent['name'] }}
                    </h4>
                    <p class="text-[12px] text-[var(--text-secondary)] leading-relaxed mb-4">
                        {{ $ent['desc'] }}
                    </p>

                    <div class="space-y-1.5 pt-3 border-t border-[var(--border-subtle)] text-[12px] mb-5">
                        <div class="flex justify-between">
                            <span class="text-[var(--text-muted)]">Resonance Freq:</span>
                            <span class="font-bold text-[#9333ea]">{{ $ent['freq'] }} kHz</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--text-muted)]">Base Drain:</span>
                            <span class="font-bold text-[#e11d48]">{{ $ent['base_drain'] }} EP</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--text-muted)]">Ecto Reward:</span>
                            <span class="font-bold text-[#059669]">₱ {{ $ent['ecto_reward'] }} Ecto</span>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <form action="/drain/execute" method="POST">
                    @csrf
                    <input type="hidden" name="entity_id" value="{{ $ent['id'] }}">
                    <button type="submit" class="btn w-full text-[12.5px] py-2 flex items-center justify-center gap-2 {{ $ent['danger'] === 'Extreme' ? 'bg-[#e11d48] hover:bg-[#be123c]' : '' }}" {{ ($user->energy <= 0 || $user->life_force <= 0) ? 'disabled' : '' }}>
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                        <span>{{ ($user->energy <= 0 || $user->life_force <= 0) ? 'Recharge Required' : 'Engage Chamber' }}</span>
                    </button>
                </form>

            </div>
        @endforeach

    </div>

@endsection