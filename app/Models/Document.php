<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model {
    use HasFactory;

    protected $fillable = [
        'document_number', 'resident_id', 'document_type', 'purpose',
        'number_of_copies', 'fee', 'payment_method', 'payment_status',
        'payment_reference', 'payment_proof', 'payment_verified_at',
        'payment_verified_by', 'payment_notes',
        'issue_date', 'status', 'viewed_at', 'released_at',
        'issued_by', 'remarks', 'rejection_reason'
    ];

    protected $casts = [
        'fee'                 => 'decimal:2',
        'issue_date'          => 'date',
        'viewed_at'           => 'datetime',
        'released_at'         => 'datetime',
        'payment_verified_at' => 'datetime',
    ];

    public function resident() { return $this->belongsTo(Resident::class); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }
    public function paymentVerifiedBy() { return $this->belongsTo(User::class, 'payment_verified_by'); }

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

    const PAYMENT_METHODS = [
        'cash'  => 'Over-the-Counter / Cash',
        'gcash' => 'GCash / Online Transfer',
        'free'  => 'Free / Waived',
    ];

    const PAYMENT_STATUSES = [
        'unpaid',
        'pending_verification',
        'verified',
        'declined',
        'waived',
    ];

    public function isPaidOrWaived(): bool {
        return in_array($this->payment_status, ['verified', 'waived']) || (float)$this->fee <= 0.00;
    }

    public function getPaymentStatusBadgeAttribute(): string {
        switch ($this->payment_status) {
            case 'verified':
                return '<span class="badge bg-success text-white"><i class="ti ti-check me-1"></i>Verified</span>';
            case 'pending_verification':
                return '<span class="badge bg-primary text-white"><i class="ti ti-clock-hour-4 me-1"></i>Pending Verification</span>';
            case 'declined':
                return '<span class="badge bg-danger text-white"><i class="ti ti-x me-1"></i>Declined</span>';
            case 'waived':
                return '<span class="badge bg-info text-white"><i class="ti ti-discount-check me-1"></i>Waived / Free</span>';
            case 'unpaid':
            default:
                return (float)$this->fee <= 0
                    ? '<span class="badge bg-info text-white"><i class="ti ti-discount-check me-1"></i>Free</span>'
                    : '<span class="badge bg-warning text-dark"><i class="ti ti-cash me-1"></i>Unpaid</span>';
        }
    }

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
