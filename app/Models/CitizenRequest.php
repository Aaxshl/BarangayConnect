<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CitizenRequest extends Model {
    use HasFactory;

    protected $fillable = [
        'tracking_number', 'resident_id', 'request_type', 'priority', 'description',
        'location', 'latitude', 'longitude', 'photo', 'status', 'assigned_to',
        'resolved_at', 'resolution_note', 'notified_at', 'viewed_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'notified_at' => 'datetime',
        'viewed_at'   => 'datetime',
    ];

    public function resident() {
        return $this->belongsTo(Resident::class);
    }

    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments() {
        return $this->hasMany(CitizenRequestComment::class)->latest();
    }

    const TYPES = [
        'broken_streetlight', 'garbage_collection', 'illegal_dumping', 'road_damage',
        'clogged_drainage', 'flooding', 'noise_complaint', 'stray_animal', 'public_safety'
    ];

    const STATUSES = ['pending', 'under_review', 'assigned', 'in_progress', 'resolved', 'closed'];

    const PRIORITIES = [
        'low'    => 'Low Priority',
        'medium' => 'Normal / Medium',
        'high'   => 'High Priority',
        'urgent' => 'Urgent / Emergency',
    ];

    public static function generateTracking() {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->count() + 1;
        return 'REQ-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if a staff user is assigned to this case and can access its private conversation.
     */
    public function canAccessConversation($user): bool {
        if (!$user) return false;
        return $this->assigned_to && (int)$this->assigned_to === (int)$user->id;
    }

    // ═══ AGING / SLA ATTRIBUTES ═══
    public function getDaysPendingAttribute(): int {
        $start = $this->created_at ?? now();
        $end = in_array($this->status, ['resolved', 'closed']) && $this->resolved_at ? $this->resolved_at : now();
        return (int) $start->diffInDays($end);
    }

    public function getAgingStatusAttribute(): string {
        if (in_array($this->status, ['resolved', 'closed'])) {
            return 'resolved';
        }
        $days = $this->days_pending;
        if ($days <= 2) return 'normal';
        if ($days <= 5) return 'warning';
        return 'overdue';
    }

    public function getAgingBadgeAttribute(): string {
        $days = $this->days_pending;
        $status = $this->aging_status;

        if ($status === 'resolved') {
            $label = $days === 0 ? 'Resolved Today' : "Resolved in {$days}d";
            return '<span class="badge bg-success text-white" style="font-size:11px"><i class="ti ti-circle-check me-1"></i>' . $label . '</span>';
        }

        if ($status === 'normal') {
            $label = $days === 0 ? 'Today (< 24h)' : "{$days}d active";
            return '<span class="badge bg-info bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:11px"><i class="ti ti-clock me-1"></i>' . $label . '</span>';
        }

        if ($status === 'warning') {
            return '<span class="badge bg-warning text-dark border border-warning" style="font-size:11px"><i class="ti ti-alert-triangle me-1"></i>' . "{$days}d Attention" . '</span>';
        }

        return '<span class="badge bg-danger text-white shadow-sm" style="font-size:11px"><i class="ti ti-alert-circle me-1"></i>' . "{$days}d Overdue" . '</span>';
    }

    public function getPriorityBadgeAttribute(): string {
        $p = $this->priority ?? 'medium';
        switch ($p) {
            case 'urgent':
                return '<span class="badge bg-danger text-white fw-bold" style="font-size:11px"><i class="ti ti-flame me-1"></i>URGENT</span>';
            case 'high':
                return '<span class="badge bg-warning text-dark fw-semibold" style="font-size:11px"><i class="ti ti-arrow-up me-1"></i>High</span>';
            case 'low':
                return '<span class="badge bg-secondary text-white" style="font-size:11px"><i class="ti ti-arrow-down me-1"></i>Low</span>';
            case 'medium':
            default:
                return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:11px"><i class="ti ti-minus me-1"></i>Medium</span>';
        }
    }
}
