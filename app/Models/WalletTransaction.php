<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id', 'top_up_request_id', 'type', 'direction', 'amount',
        'balance_before', 'balance_after', 'description',
    ];

    protected $casts = [
        'amount' => 'integer', 'balance_before' => 'integer', 'balance_after' => 'integer',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function topUpRequest() { return $this->belongsTo(TopUpRequest::class); }
}
