@extends('layouts.app')

@section('title', 'Demo Top-up Requests — Admin')

@section('content')
<div class="mb-7">
    <h1 class="text-[24px] font-semibold text-[var(--text)] mb-1">Demo Top-up Requests</h1>
    <p class="text-[13.5px] text-[var(--text-secondary)]">Review simulated Ecto requests. No real payments are involved.</p>
</div>

@if (session('success'))
    <div role="status" class="mb-5 rounded-xl border border-[var(--success)]/30 bg-[var(--success-bg)] p-4 text-sm text-[var(--success)]">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div role="alert" class="mb-5 rounded-xl border border-[var(--danger)]/30 bg-[var(--danger-bg)] p-4 text-sm text-[var(--danger)]">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="card mb-5 p-4 text-red-600">{{ $errors->first() }}</div>
@endif

<div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 mb-5 shadow-sm">
    <form method="GET" action="/admin/top-ups" class="flex flex-wrap items-end gap-3">
        <div>
            <label for="status" class="block text-xs font-semibold uppercase tracking-[0.08em] text-[var(--text-muted)] mb-2">Filter by status</label>
            <select id="status" name="status" class="min-w-44 rounded-lg border border-[var(--border)] bg-[var(--bg)] px-3 py-2.5 text-sm text-[var(--text)] shadow-none outline-none transition focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/15">
                <option value="">All statuses</option>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button class="inline-flex items-center gap-2 rounded-lg border border-[var(--border)] bg-[var(--surface-soft)] px-4 py-2.5 text-sm font-semibold text-[var(--text)] transition hover:bg-[var(--surface-hover)]" type="submit"><i data-lucide="list-filter" class="h-4 w-4"></i> Apply filter</button>
    </form>
</div>

<div class="card p-5">
    @if ($requests->isEmpty())
        <p class="text-sm text-[var(--text-secondary)]">No requests match this filter.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left border-b"><th class="py-2 pr-3">User</th><th class="py-2 pr-3">Requested</th><th class="py-2 pr-3">Amount</th><th class="py-2 pr-3">Status</th><th class="py-2 pr-3">Review info</th><th class="py-2">Actions</th></tr></thead>
                <tbody>
                @foreach ($requests as $topUp)
                    <tr class="border-b align-top">
                        <td class="py-3 pr-3">{{ $topUp->user->name }}<div class="text-xs text-[var(--text-muted)]">{{ $topUp->user->email }}</div></td>
                        <td class="py-3 pr-3">{{ $topUp->created_at->format('M d, Y H:i') }}</td>
                        <td class="py-3 pr-3">{{ number_format($topUp->amount) }} Ecto</td>
                        <td class="py-3 pr-3">{{ ucfirst($topUp->status) }}</td>
                        <td class="py-3 pr-3">
                            @if ($topUp->reviewer)
                                {{ $topUp->reviewer->name }}<div class="text-xs text-[var(--text-muted)]">{{ optional($topUp->reviewed_at)->format('M d, Y H:i') }}</div>
                            @endif
                            @if ($topUp->rejection_reason)
                                <div class="text-xs mt-1">{{ $topUp->rejection_reason }}</div>
                            @endif
                        </td>
                        <td class="py-3 min-w-[220px]">
                            @if ($topUp->status === 'pending')
                                <form method="POST" action="/admin/top-ups/{{ $topUp->id }}/approve" class="mb-2">
                                    @csrf
                                    <button type="submit" class="rounded px-3 py-2 border">Approve demo credit</button>
                                </form>
                                <form method="POST" action="/admin/top-ups/{{ $topUp->id }}/reject" class="flex flex-col gap-2">
                                    @csrf
                                    <input type="text" name="reason" maxlength="500" placeholder="Optional rejection reason" class="rounded border p-2">
                                    <button type="submit" class="rounded px-3 py-2 border">Reject request</button>
                                </form>
                            @else
                                <span class="text-xs text-[var(--text-muted)]">Already reviewed</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
