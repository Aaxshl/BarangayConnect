<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'contact_number', 'avatar', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    const ROLES = [
        'captain',
        'administrator',
        'councilor',
        'secretary',
        'staff',
        'sk_chairman',
        'sk_councilor',
    ];

    const ROLE_LABELS = [
        'captain'       => 'Punong Barangay (Captain)',
        'administrator' => 'System Administrator',
        'councilor'     => 'Barangay Kagawad (Councilor)',
        'secretary'     => 'Barangay Secretary',
        'staff'         => 'Barangay Staff / Tanod',
        'sk_chairman'   => 'SK Chairman',
        'sk_councilor'  => 'SK Councilor',
    ];

    public function getRoleLabelAttribute(): string {
        return self::ROLE_LABELS[$this->role] ?? ucwords(str_replace('_', ' ', $this->role));
    }

    /**
     * Check dynamic permission against the active matrix.
     * Captain & System Admin always return true (Super-Admin immunity).
     */
    public function canDo(string $permission): bool {
        if ($this->isCapOrAdmin()) {
            return true;
        }

        $matrix = Setting::getPermissionsMatrix();
        return !empty($matrix[$this->role][$permission]);
    }

    public function isAdmin(): bool {
        return $this->role === 'administrator';
    }

    public function isCaptain(): bool {
        return $this->role === 'captain';
    }

    public function isCapOrAdmin(): bool {
        return in_array($this->role, ['captain', 'administrator']);
    }

    public function isCouncil(): bool {
        return in_array($this->role, ['captain', 'administrator', 'councilor']);
    }

    public function isSecretary(): bool {
        return $this->role === 'secretary';
    }

    public function isStaff(): bool {
        return $this->role === 'staff';
    }

    public function isSK(): bool {
        return in_array($this->role, ['sk_chairman', 'sk_councilor']);
    }

    public function isSkChairman(): bool {
        return $this->role === 'sk_chairman';
    }

    public function isSkCouncilor(): bool {
        return $this->role === 'sk_councilor';
    }

    public function canPublishBarangay(): bool {
        return $this->canDo('announcements.publish');
    }

    public function canManageUsers(): bool {
        return in_array($this->role, ['captain', 'administrator']);
    }

    public function canAccessAdminPanel(): bool {
        return !in_array($this->role, ['sk_chairman', 'sk_councilor']);
    }

    public function canAccessSkPortal(): bool {
        return in_array($this->role, ['sk_chairman', 'sk_councilor', 'captain', 'administrator']);
    }

    public function serviceLogs() {
        return $this->hasMany(ServiceLog::class, 'assigned_to');
    }

    public function citizenRequests() {
        return $this->hasMany(CitizenRequest::class, 'assigned_to');
    }

    public function documents() {
        return $this->hasMany(Document::class, 'issued_by');
    }
}
