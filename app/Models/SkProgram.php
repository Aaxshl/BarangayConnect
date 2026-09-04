<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SkProgram extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
        'location',
        'budget',
        'target_participants',
        'start_date',
        'end_date',
        'status',
        'coordinator_id',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'budget'     => 'decimal:2',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    const CATEGORIES = [
        'sports_and_wellness'         => 'Sports & Wellness',
        'education_and_scholarship'   => 'Education & Scholarship',
        'leadership_and_governance'   => 'Leadership & Governance',
        'environmental_protection'    => 'Environmental Protection',
        'health_and_anti_drug'        => 'Health & Anti-Drug Abuse',
        'arts_and_culture'            => 'Arts & Culture',
        'livelihood_and_skills'       => 'Livelihood & Skills Training',
    ];

    const STATUSES = [
        'proposed'  => 'Proposed',
        'approved'  => 'Approved',
        'ongoing'   => 'Ongoing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function coordinator() {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string {
        return self::CATEGORIES[$this->category] ?? ucwords(str_replace('_', ' ', $this->category));
    }

    public function getStatusLabelAttribute(): string {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClassAttribute(): string {
        return match($this->status) {
            'proposed'  => 'bg-secondary',
            'approved'  => 'bg-info text-dark',
            'ongoing'   => 'bg-primary',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default     => 'bg-secondary',
        };
    }

    public function scopeActive($query) {
        return $query->whereIn('status', ['approved', 'ongoing']);
    }

    public function scopeOngoing($query) {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'completed');
    }

    public function scopeProposed($query) {
        return $query->where('status', 'proposed');
    }
}
