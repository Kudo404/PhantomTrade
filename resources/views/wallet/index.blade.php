@extends('layouts.app')

@section('title', 'My Wallet — Phantom Trade')

@section('content')
<div class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)] px-3 py-1 text-xs font-medium text-[var(--text-secondary)]">
            <span class="h-2 w-2 rounded-full bg-[var(--cyan)]"></span>
            PhantomTrade Economy
        </div>
        <h1 class="text-[26px] font-semibold tracking-tight text-[var(--text)]">Ecto Wallet</h1>
        <p class="mt-1 max-w-2xl text-[13.5px] text-[var(--text-secondary)]">Manage your spectral currency and review wallet activity.</p>
    </div>
    <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-xs text-[var(--text-muted)]">
        <span class="h-2 w-2 rounded-full bg-[var(--warning)]"></span>
        Demo mode · No real payments
    </span>
</div>

@if (session('success'))
    <div role="status" class="mb-5 flex items-start gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 text-sm text-[var(--text)] shadow-sm">
        <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[var(--accent-soft)] text-[var(--accent)]">✓</span>
        <div>{{ session('success') }}</div>
    </div>
@endif
@if ($errors->any())
    <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-[var(--danger)]/30 bg-[var(--danger-bg)] p-4 text-sm text-[var(--danger)]">
        <span class="font-semibold">!</span>
        <div>{{ $errors->first() }}</div>
    </div>
@endif

<div class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-5">
    <section class="relative overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm lg:col-span-3 sm:p-8">
        <div class="pointer-events-none absolute -right-12 -top-16 h-48 w-48 rounded-full bg-[var(--accent-soft)] opacity-30 blur-3xl"></div>
        <div class="relative">
            <div class="flex items-center gap-2 text-sm font-medium text-[var(--text-secondary)]">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--accent-soft)] text-[var(--accent)]">✦</span>
                Available balance
            </div>
            <div class="mt-5 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                <span class="text-4xl font-semibold tracking-tight text-[var(--text)] sm:text-5xl">{{ number_format($user->ecto) }}</span>
                <span class="text-base font-medium text-[var(--text-secondary)]">ECTO</span>
            </div>
            <p class="mt-4 max-w-lg text-sm leading-6 text-[var(--text-secondary)]">Your balance is ready for eligible PhantomTrade activity. Demo top-ups are added only after an administrator approves your request.</p>
            <div class="mt-6 flex flex-wrap items-center gap-2 text-xs text-[var(--text-muted)]">
                <span class="rounded-md border border-[var(--border)] px-2.5 py-1.5">Secure approval flow</span>
                <span class="rounded-md border border-[var(--border)] px-2.5 py-1.5">Wallet activity ledger</span>
            </div>
        </div>
    </section>

    @unless (auth()->user()->isAdmin())
        <section class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm lg:col-span-2 sm:p-7">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-[var(--text)]">Request Ecto</h2>
                    <p class="mt-1 text-sm leading-5 text-[var(--text-secondary)]">Submit a demo top-up for administrator review.</p>
                </div>
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[var(--accent-soft)] text-lg text-[var(--accent)]"><i data-lucide="wallet"></i></span>
            </div>
            <form method="POST" action="{{ url('/wallet') }}" class="mt-5 space-y-4" id="ecto-top-up-form">
                @csrf
                <div>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Choose an amount</span>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ([100, 250, 500, 1000] as $preset)
                            <button type="button" data-ecto-preset="{{ $preset }}" class="ecto-preset rounded-lg border border-[var(--border)] bg-[var(--surface-soft)] px-3 py-2.5 text-sm font-semibold text-[var(--text-secondary)] transition hover:border-[var(--accent)] hover:text-[var(--text)]">
                                {{ number_format($preset) }} <span class="text-xs font-medium">Ecto</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label for="amount" class="mb-2 block text-xs font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)]">Custom amount</label>
                    <div class="relative">
                        <input id="amount" name="amount" type="number" min="10" max="10000" step="1" required value="{{ old('amount', 100) }}" placeholder="Enter amount…" class="w-full rounded-lg border border-[var(--border)] bg-[var(--bg)] px-3.5 py-3 pr-20 text-sm text-[var(--text)] outline-none transition placeholder:text-[var(--text-muted)] focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/15">
                        <span class="pointer-events-none absolute inset-y-0 right-3.5 flex items-center text-xs font-semibold tracking-wide text-[var(--text-muted)]">ECTO</span>
                    </div>
                    @error('amount')
                        <p class="mt-2 text-xs text-[var(--danger)]">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-primary inline-flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold transition">
                    Submit demo request <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
            </form>
            <p class="mt-4 flex items-start gap-2 border-t border-[var(--border)] pt-4 text-xs leading-5 text-[var(--text-muted)]"><i data-lucide="info" class="mt-0.5 h-3.5 w-3.5 shrink-0"></i><span>Demo only. No real payment is processed. Ecto is credited only after an administrator approves your request.</span></p>
            <script>
                (() => {
                    const form = document.getElementById('ecto-top-up-form');
                    if (!form) return;
                    const amount = form.querySelector('#amount');
                    const presets = [...form.querySelectorAll('[data-ecto-preset]')];
                    const sync = () => {
                        presets.forEach((button) => {
                            const selected = Number(button.dataset.ectoPreset) === Number(amount.value);
                            button.style.background = selected ? 'var(--accent-soft)' : 'var(--surface-soft)';
                            button.style.borderColor = selected ? 'var(--accent)' : 'var(--border)';
                            button.style.color = selected ? 'var(--accent)' : 'var(--text-secondary)';
                        });
                    };
                    presets.forEach((button) => button.addEventListener('click', () => {
                        amount.value = button.dataset.ectoPreset;
                        sync();
                    }));
                    amount.addEventListener('input', sync);
                    sync();
                })();
            </script>
        </section>
    @else
        <section class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm lg:col-span-2 sm:p-7">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--accent-soft)] text-[var(--accent)]"><i data-lucide="shield-check"></i></div>
            <h2 class="mt-4 text-base font-semibold text-[var(--text)]">Administrator wallet</h2>
            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Administrator accounts review user top-up requests instead of submitting their own. Use the review queue to approve or reject pending demo requests.</p>
            <a href="/admin/top-ups?status=pending" class="btn-primary mt-5 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold">Review pending requests <span aria-hidden="true">→</span></a>
        </section>
    @endunless
</div>

<section class="mb-6 overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
    <div class="flex flex-col gap-2 border-b border-[var(--border)] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div>
            <h2 class="text-base font-semibold text-[var(--text)]">Top-up requests</h2>
            <p class="mt-1 text-sm text-[var(--text-secondary)]">Follow the review status of your submitted requests.</p>
        </div>
        <span class="text-xs text-[var(--text-muted)]">{{ $requests->count() }} {{ \Illuminate\Support\Str::plural('request', $requests->count()) }}</span>
    </div>
    @if ($requests->isEmpty())
        <div class="px-6 py-10 text-center">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-[var(--surface-soft)] text-xl text-[var(--text-muted)]">↗</div>
            <p class="mt-3 text-sm font-medium text-[var(--text)]">No top-up requests yet</p>
            <p class="mt-1 text-sm text-[var(--text-secondary)]">Your submitted requests will appear here.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-[var(--surface-soft)] text-xs uppercase tracking-wide text-[var(--text-muted)]">
                    <tr><th class="px-5 py-3 font-semibold sm:px-6">Date</th><th class="px-5 py-3 font-semibold">Amount</th><th class="px-5 py-3 font-semibold">Method</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold sm:pr-6">Reason</th></tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                @foreach ($requests as $topUp)
                    <tr class="transition hover:bg-[var(--surface-hover)]">
                        <td class="whitespace-nowrap px-5 py-4 text-[var(--text-secondary)] sm:px-6">{{ $topUp->created_at->format('M d, Y H:i') }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-[var(--text)]">{{ number_format($topUp->amount) }} <span class="text-xs font-normal text-[var(--text-muted)]">Ecto</span></td>
                        <td class="px-5 py-4 text-[var(--text-secondary)]">Demo</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $topUp->status === 'approved' ? 'bg-[var(--success)]/10 text-[var(--success)]' : ($topUp->status === 'rejected' ? 'bg-[var(--danger-bg)] text-[var(--danger)]' : 'bg-[var(--warning)]/10 text-[var(--warning)]') }}">
                                {{ ucfirst($topUp->status) }}
                            </span>
                        </td>
                        <td class="max-w-xs px-5 py-4 text-[var(--text-secondary)] sm:pr-6">{{ $topUp->rejection_reason ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

<section class="overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
    <div class="border-b border-[var(--border)] px-5 py-5 sm:px-6">
        <h2 class="text-base font-semibold text-[var(--text)]">Transaction ledger</h2>
        <p class="mt-1 text-sm text-[var(--text-secondary)]">A record of wallet transactions captured by the ledger.</p>
    </div>
    @if ($transactions->isEmpty())
        <div class="px-6 py-10 text-center">
            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-[var(--surface-soft)] text-xl text-[var(--text-muted)]">≋</div>
            <p class="mt-3 text-sm font-medium text-[var(--text)]">No wallet transactions recorded</p>
            <p class="mt-1 text-sm text-[var(--text-secondary)]">Existing purchases are not backfilled into this ledger.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-left text-sm">
                <thead class="bg-[var(--surface-soft)] text-xs uppercase tracking-wide text-[var(--text-muted)]">
                    <tr><th class="px-5 py-3 font-semibold sm:px-6">Date</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold">Amount</th><th class="px-5 py-3 font-semibold">Balance after</th><th class="px-5 py-3 font-semibold sm:pr-6">Details</th></tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                @foreach ($transactions as $transaction)
                    <tr class="transition hover:bg-[var(--surface-hover)]">
                        <td class="whitespace-nowrap px-5 py-4 text-[var(--text-secondary)] sm:px-6">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-4 text-[var(--text)]">{{ str_replace('_', ' ', ucfirst($transaction->type)) }}</td>
                        <td class="whitespace-nowrap px-5 py-4 font-semibold {{ $transaction->direction === 'credit' ? 'text-[var(--success)]' : 'text-[var(--danger)]' }}">{{ $transaction->direction === 'credit' ? '+' : '−' }}{{ number_format($transaction->amount) }} <span class="text-xs font-normal text-[var(--text-muted)]">Ecto</span></td>
                        <td class="whitespace-nowrap px-5 py-4 text-[var(--text)]">{{ number_format($transaction->balance_after) }}</td>
                        <td class="px-5 py-4 text-[var(--text-secondary)] sm:pr-6">{{ $transaction->description ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
