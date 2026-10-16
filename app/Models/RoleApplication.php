<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleApplication extends Model
{
    protected $fillable = [
        'user_id',
        'target_role',
        'status',
        'form_data',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'form_data'   => 'array',
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Convenience ───────────────────────────
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function targetRoleLabel(): string
    {
        return match ($this->target_role) {
            'merchant'     => 'Merchant',
            'ghost_hunter' => 'Ghost Hunter',
            default        => ucfirst($this->target_role),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'approved' => '#059669',
            'rejected' => '#e11d48',
            default    => '#d97706',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default    => 'Pending',
        };
    }
}