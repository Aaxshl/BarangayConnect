<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;
    protected $fillable = [
        'name','email','password','role','contact_number','avatar','status'
    ];
    protected $hidden = ['password','remember_token'];
    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed'];

    const ROLES = ['administrator','secretary','staff'];

    public function isAdmin() { return $this->role === 'administrator'; }
    public function isSecretary() { return in_array($this->role,['administrator','secretary']); }
    public function serviceLogs() { return $this->hasMany(ServiceLog::class,'assigned_to'); }
    public function citizenRequests() { return $this->hasMany(CitizenRequest::class,'assigned_to'); }
    public function documents() { return $this->hasMany(Document::class,'issued_by'); }
}
