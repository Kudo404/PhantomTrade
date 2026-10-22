<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUpRequest extends Model
{
    protected $fillable = ['user_id', 'amount', 'method', 'status', 'reviewed_by', 'rejection_reason', 'reviewed_at'];

    protected $casts = ['amount' => 'integer', 'reviewed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function transaction() { return $this->hasOne(WalletTransaction::class); }
}
