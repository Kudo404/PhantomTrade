<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── Helpers ──────────────────────────────
    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }

    public function isFrom(User $user): bool
    {
        return $this->sender_id === $user->id;
    }
}