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
}
