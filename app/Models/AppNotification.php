<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    // The table Laravel looks for is 'app_notifications' by default.
    // Ours is called 'notifications', so we tell it explicitly.
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'icon',
        'url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Convenience ───────────────────────────
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function timeAgo(): string
    {
        return $this->created_at->diffForHumans();
    }
}