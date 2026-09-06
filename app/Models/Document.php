<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model {
    use HasFactory;

    protected $fillable = [
        'document_number', 'resident_id', 'document_type', 'purpose',
        'number_of_copies', 'issue_date', 'status', 'viewed_at', 'released_at',
        'issued_by', 'remarks', 'rejection_reason'
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'viewed_at'   => 'datetime',
        'released_at' => 'datetime',
    ];

    public function resident() { return $this->belongsTo(Resident::class); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }

    const TYPES = [
        'barangay_clearance'    => 'Barangay Clearance',
        'certificate_residency' => 'Certificate of Residency',
        'certificate_indigency' => 'Certificate of Indigency',
        'business_clearance'    => 'Business Clearance',
        'barangay_permit'       => 'Barangay Permit',
        'other'                 => 'Other Certificate',
    ];

    const STATUSES = [
        'pending',
        'under_review',
        'processing',
        'ready_for_pickup',
        'released',
        'cancelled'
    ];

    // ═══ AGING / SLA ATTRIBUTES ═══
    public function getDaysInProcessingAttribute(): int {
        $start = $this->created_at ?? ($this->issue_date ? $this->issue_date->startOfDay() : now());
        $end = in_array($this->status, ['released', 'cancelled']) && $this->released_at ? $this->released_at : now();
        return (int) $start->diffInDays($end);
    }

    public function getAgingStatusAttribute(): string {
        if (in_array($this->status, ['released', 'cancelled'])) {
            return 'completed';
        }
        $days = $this->days_in_processing;
        if ($days <= 2) return 'normal';
        if ($days <= 5) return 'warning';
        return 'overdue';
    }

    public function getAgingBadgeAttribute(): string {
        $days = $this->days_in_processing;
        $status = $this->aging_status;

        if ($status === 'completed') {
            $label = $this->status === 'released' ? ($days === 0 ? 'Released Same Day' : "Released in {$days}d") : 'Cancelled';
            return '<span class="badge bg-success text-white" style="font-size:11px"><i class="ti ti-circle-check me-1"></i>' . $label . '</span>';
        }

        if ($status === 'normal') {
            $label = $days === 0 ? 'Today (< 24h)' : "{$days}d processing";
            return '<span class="badge bg-info bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:11px"><i class="ti ti-clock me-1"></i>' . $label . '</span>';
        }

        if ($status === 'warning') {
            return '<span class="badge bg-warning text-dark border border-warning" style="font-size:11px"><i class="ti ti-alert-triangle me-1"></i>' . "{$days}d Attention" . '</span>';
        }

        return '<span class="badge bg-danger text-white shadow-sm" style="font-size:11px"><i class="ti ti-alert-circle me-1"></i>' . "{$days}d Overdue" . '</span>';
    }
}
