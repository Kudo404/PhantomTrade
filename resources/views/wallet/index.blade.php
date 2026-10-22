@extends('layouts.app')

@section('title', 'My Wallet — Phantom Trade')

@section('content')
<div class="mb-7">
    <h1 class="text-[24px] font-semibold text-[var(--text)] mb-1">My Wallet</h1>
    <p class="text-[13.5px] text-[var(--text-secondary)]">Demo-only Ecto credits. No real payments are processed.</p>
</div>

@if (session('success'))
    <div class="card mb-5 p-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="card mb-5 p-4 text-red-600">{{ $errors->first() }}</div>
@endif

<div class="card p-6 mb-6">
    <div class="text-[11px] uppercase font-semibold tracking-wider text-[var(--text-muted)]">Available Ecto</div>
    <div class="text-[32px] font-semibold mt-2">{{ number_format($user->ecto) }} <span class="text-sm">Ecto</span></div>
    <p class="text-sm text-[var(--text-secondary)] mt-2">Request simulated credits below. Your balance changes only after an administrator approves the request.</p>
</div>

<div class="card p-6 mb-6">
    <h2 class="text-lg font-semibold mb-3">Request demo top-up</h2>
    <form method="POST" action="/wallet" class="flex flex-wrap items-end gap-3">
        @csrf
        <div class="min-w-[220px]">
            <label for="amount" class="block text-sm mb-1">Amount (10–10,000 Ecto)</label>
            <input id="amount" name="amount" type="number" min="10" max="10000" step="1" required value="{{ old('amount', 100) }}" class="w-full rounded border p-2">
        </div>
        <button type="submit" class="btn-primary rounded px-4 py-2">Submit demo request</button>
    </form>
    <p class="text-xs text-[var(--text-muted)] mt-3">Simulation only — no GCash, bank transfer, or payment gateway is connected.</p>
</div>

<div class="card p-6 mb-6">
    <h2 class="text-lg font-semibold mb-3">My top-up requests</h2>
    @if ($requests->isEmpty())
        <p class="text-sm text-[var(--text-secondary)]">No top-up requests yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b"><th class="py-2 pr-3">Date</th><th class="py-2 pr-3">Amount</th><th class="py-2 pr-3">Method</th><th class="py-2 pr-3">Status</th><th class="py-2">Reason</th></tr></thead>
                <tbody>
                @foreach ($requests as $topUp)
                    <tr class="border-b">
                        <td class="py-3 pr-3">{{ $topUp->created_at->format('M d, Y H:i') }}</td>
                        <td class="py-3 pr-3">{{ number_format($topUp->amount) }} Ecto</td>
                        <td class="py-3 pr-3">Demo</td>
                        <td class="py-3 pr-3">{{ ucfirst($topUp->status) }}</td>
                        <td class="py-3">{{ $topUp->rejection_reason ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="card p-6">
    <h2 class="text-lg font-semibold mb-3">Transaction ledger</h2>
    @if ($transactions->isEmpty())
        <p class="text-sm text-[var(--text-secondary)]">No wallet transactions recorded yet. Existing purchases are not backfilled into this ledger.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b"><th class="py-2 pr-3">Date</th><th class="py-2 pr-3">Type</th><th class="py-2 pr-3">Amount</th><th class="py-2 pr-3">Balance after</th><th class="py-2">Details</th></tr></thead>
                <tbody>
                @foreach ($transactions as $transaction)
                    <tr class="border-b">
                        <td class="py-3 pr-3">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td class="py-3 pr-3">{{ str_replace('_', ' ', ucfirst($transaction->type)) }}</td>
                        <td class="py-3 pr-3">{{ $transaction->direction === 'credit' ? '+' : '−' }}{{ number_format($transaction->amount) }} Ecto</td>
                        <td class="py-3 pr-3">{{ number_format($transaction->balance_after) }}</td>
                        <td class="py-3">{{ $transaction->description ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
