<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceLog extends Model {
    use HasFactory;

    protected $fillable = [
        'log_number',
        'service_type',
        'resident_id',
        'description',
        'date_of_service',
        'status',
        'assigned_to',
        'remarks',
        'created_by',
        'resolution_notes',
        'cancellation_reason',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'date_of_service' => 'date',
        'resolved_at'     => 'datetime',
        'closed_at'       => 'datetime',
    ];

    public function resident() {
        return $this->belongsTo(Resident::class);
    }

    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    const TYPES = [
        'complaint',
        'mediation',
        'community_assistance',
        'health_services',
        'community_programs',
        'infrastructure',
        'barangay_activity',
    ];

    const STATUSES = [
        'pending',
        'assigned',
        'in_progress',
        'resolved',
        'closed',
        'cancelled',
    ];
}
