@extends('layouts.app')

@section('title', 'Checkout — Phantom Trade')

@section('content')
<div style="max-width:760px;margin:0 auto;padding:12px 0;">
    <a href="{{ url('/gears/'.$gear->id) }}" style="color:var(--text-secondary);text-decoration:none;">← Back to gear</a>

    <h1 style="font-size:28px;font-weight:700;color:var(--text);margin:18px 0 6px;">Checkout</h1>
    <p style="color:var(--text-secondary);margin:0 0 24px;">Review your order and confirm payment with your ecto balance.</p>

    @if ($errors->any())
        <div role="alert" style="padding:12px 16px;margin-bottom:16px;color:#991b1b;background:#fee2e2;border-radius:10px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card" style="padding:22px;margin-bottom:18px;">
        <h2 style="font-size:17px;font-weight:650;color:var(--text);margin:0 0 16px;">Order summary</h2>
        <div style="display:flex;gap:16px;align-items:center;">
            @if ($gear->image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($gear->image_path) }}" alt="{{ $gear->name }}" style="width:104px;height:104px;object-fit:cover;border-radius:12px;border:1px solid var(--border);">
            @else
                <div style="width:104px;height:104px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:var(--bg);font-size:32px;">
                    @switch($gear->type)
                        @case('trap') 🎯 @break
                        @case('ward') 🛡️ @break
                        @case('anchor') ⚓ @break
                        @case('scanner') 📡 @break
                        @default ⚙️
                    @endswitch
                </div>
            @endif
            <div style="flex:1;min-width:0;">
                <div style="font-weight:650;color:var(--text);font-size:18px;">{{ $gear->name }}</div>
                <div style="color:var(--text-secondary);font-size:13px;margin-top:4px;">{{ ucfirst($gear->type) }} · Quantity: 1</div>
                <div style="color:var(--text-secondary);font-size:13px;margin-top:8px;">{{ \Illuminate\Support\Str::limit($gear->description, 110) }}</div>
            </div>
        </div>
        <div style="border-top:1px solid var(--border);margin-top:20px;padding-top:16px;display:flex;justify-content:space-between;gap:12px;">
            <span style="color:var(--text-secondary);">Total due</span>
            <strong style="font-size:22px;color:var(--text);">₱{{ number_format($gear->ecto_cost) }} ecto</strong>
        </div>
    </div>

    <div class="card" style="padding:22px;margin-bottom:18px;">
        <h2 style="font-size:17px;font-weight:650;color:var(--text);margin:0 0 14px;">Payment method</h2>
        <div style="display:flex;align-items:center;gap:12px;padding:14px;border:1px solid var(--border);border-radius:10px;background:var(--bg);">
            <div style="font-size:24px;">💎</div>
            <div style="flex:1;">
                <div style="font-weight:600;color:var(--text);">Phantom Ecto Wallet</div>
                <div style="font-size:13px;color:var(--text-secondary);">In-game currency · No card details required</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:12px;color:var(--text-secondary);">Available balance</div>
                <strong style="color:var(--text);">₱{{ number_format($user->ecto) }}</strong>
            </div>
        </div>
        @if ((int) $user->ecto < (int) $gear->ecto_cost)
            <p style="margin:12px 0 0;color:#dc2626;font-size:13px;">Your balance is too low to complete this purchase.</p>
        @else
            <p style="margin:12px 0 0;color:var(--text-secondary);font-size:13px;">Your balance after purchase will be ₱{{ number_format((int) $user->ecto - (int) $gear->ecto_cost) }}.</p>
        @endif
    </div>

    <form method="POST" action="{{ url('/gears/'.$gear->id.'/buy') }}" class="card" style="padding:18px;display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;">
        @csrf
        <div style="font-size:12px;color:var(--text-secondary);max-width:340px;">By confirming, the price will be deducted from your ecto balance and the gear will be added to your inventory.</div>
        <button type="submit" class="btn" @if ((int) $user->ecto < (int) $gear->ecto_cost) disabled aria-disabled="true" @endif style="padding:12px 18px;cursor:pointer;">
            Confirm & Pay ₱{{ number_format($gear->ecto_cost) }}
        </button>
    </form>
</div>
@endsection
