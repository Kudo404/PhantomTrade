<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    // ── Helpers ──────────────────────────────

    /**
     * Find or create a conversation between two users.
     * Stores them in a consistent order (lower ID first).
     */
    public static function findOrCreateBetween(User $a, User $b): self
    {
        if ($a->id === $b->id) {
            throw new \InvalidArgumentException('Cannot create a conversation with yourself.');
        }

        $oneId = min($a->id, $b->id);
        $twoId = max($a->id, $b->id);

        return static::firstOrCreate(
            ['user_one_id' => $oneId, 'user_two_id' => $twoId],
            ['last_message_at' => now()]
        );
    }

    /**
     * Get the other participant relative to a given user.
     */
    public function otherUser(User $me): ?User
    {
        if ($me->id === $this->user_one_id) return $this->userTwo;
        if ($me->id === $this->user_two_id) return $this->userOne;
        return null;
    }

    /**
     * Is the given user part of this conversation?
     */
    public function hasUser(User $user): bool
    {
        return $user->id === $this->user_one_id || $user->id === $this->user_two_id;
    }

    /**
     * Count unread messages for the given user.
     */
    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}