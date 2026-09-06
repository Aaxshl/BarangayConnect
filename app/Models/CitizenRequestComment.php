<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitizenRequestComment extends Model {
    use HasFactory;

    protected $fillable = [
        'citizen_request_id',
        'user_id',
        'resident_id',
        'sender_type',
        'message',
        'attachment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function citizenRequest() {
        return $this->belongsTo(CitizenRequest::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function resident() {
        return $this->belongsTo(Resident::class);
    }

    public function getSenderNameAttribute(): string {
        if ($this->sender_type === 'official') {
            return $this->user ? $this->user->name : 'Barangay Official';
        }
        return $this->resident ? $this->resident->full_name : 'Resident';
    }

    public function getSenderBadgeAttribute(): string {
        if ($this->sender_type === 'official') {
            return $this->user ? $this->user->role_label : 'Barangay Office';
        }
        return 'Citizen Resident';
    }
}
