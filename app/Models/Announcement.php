<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'image',
        'announcement_type',
        'status',
        'published_at',
        'archived_at',
        'created_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at'  => 'datetime',
    ];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    const TYPES = [
        'community_event',
        'health_advisory',
        'public_advisory',
        'emergency_notice',
        'general',
    ];

    const STATUSES = [
        'draft',
        'published',
        'scheduled',
        'archived',
    ];

    /**
     * Scope announcements that should be visible to residents/public.
     */
    public function scopePublished($query) {
        return $query->where(function ($q) {
            $q->where('status', 'published')
              ->orWhere(function ($sq) {
                  $sq->where('status', 'scheduled')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
              });
        });
    }

    /**
     * Helper to check if announcement is live.
     */
    public function isLive(): bool {
        if ($this->status === 'published') return true;
        if ($this->status === 'scheduled' && $this->published_at && $this->published_at->isPast()) return true;
        return false;
    }
}
