<?php

namespace App\Http\Controllers;

use App\Models\TopUpRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $requests = TopUpRequest::where('user_id', $user->id)->latest()->take(20)->get();
        $transactions = WalletTransaction::where('user_id', $user->id)->latest()->take(50)->get();

        return view('wallet.index', compact('user', 'requests', 'transactions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:10|max:10000',
        ]);

        TopUpRequest::create([
            'user_id' => auth()->id(),
            'amount' => (int) $validated['amount'],
            'method' => 'demo',
            'status' => 'pending',
        ]);

        return redirect('/wallet')->with('success', 'Demo top-up request submitted. Ecto is added only after admin approval.');
    }

    public function adminIndex(Request $request)
    {
        $query = TopUpRequest::with(['user', 'reviewer'])->latest();

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.topups.index', compact('requests'));
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            $topUp = TopUpRequest::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($topUp->status !== 'pending') {
                throw ValidationException::withMessages(['top_up' => 'This request has already been reviewed.']);
            }

            $amount = (int) $topUp->amount;
            if ($amount < 10 || $amount > 10000) {
                throw ValidationException::withMessages(['top_up' => 'The amount is outside the allowed demo range.']);
            }

            $user = User::whereKey($topUp->user_id)->lockForUpdate()->firstOrFail();
            $before = (int) $user->ecto;
            $after = $before + $amount;

            $user->increment('ecto', $amount);

            WalletTransaction::create([
                'user_id' => $user->id,
                'top_up_request_id' => $topUp->id,
                'type' => 'demo_top_up',
                'direction' => 'credit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => 'Admin-approved simulated top-up',
            ]);

            $topUp->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);
        });

        return redirect('/admin/top-ups')->with('success', 'Demo top-up approved and Ecto credited.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate(['reason' => 'nullable|string|max:500']);

        DB::transaction(function () use ($id, $validated) {
            $topUp = TopUpRequest::whereKey($id)->lockForUpdate()->firstOrFail();

            if ($topUp->status !== 'pending') {
                throw ValidationException::withMessages(['top_up' => 'This request has already been reviewed.']);
            }

            $topUp->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => $validated['reason'] ?? null,
            ]);
        });

        return redirect('/admin/top-ups')->with('success', 'Request rejected. No Ecto was added.');
    }
}
