<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resident extends Model {
    use HasFactory;
    protected $fillable = [
        'first_name','last_name','middle_name','birthdate','age','gender',
        'civil_status','address','purok','zone','contact_number','occupation',
        'household_id','status','qr_code','photo','created_by'
    ];
    protected $casts = ['birthdate' => 'date'];

    public function household() { return $this->belongsTo(Household::class); }
    public function documents() { return $this->hasMany(Document::class); }
    public function serviceLogs() { return $this->hasMany(ServiceLog::class); }
    public function citizenRequests() { return $this->hasMany(CitizenRequest::class); }
    public function getFullNameAttribute() { return "{$this->first_name} {$this->middle_name} {$this->last_name}"; }
    public function getAgeAttribute() {
        return $this->birthdate ? $this->birthdate->age : $this->attributes['age'];
    }
    public function scopeActive($query) { return $query->where('status','active'); }
    public function scopeSearch($query,$term) {
        return $query->where(function($q) use ($term) {
            $q->where('first_name','like',"%{$term}%")
              ->orWhere('last_name','like',"%{$term}%")
              ->orWhere('contact_number','like',"%{$term}%");
        });
    }
}
