<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceLog extends Model {
    use HasFactory;
    protected $fillable = [
        'log_number','service_type','resident_id','description',
        'date_of_service','status','assigned_to','remarks','created_by'
    ];
    protected $casts = ['date_of_service' => 'date'];

    public function resident() { return $this->belongsTo(Resident::class); }
    public function assignedTo() { return $this->belongsTo(User::class,'assigned_to'); }

    const TYPES = [
        'complaint','mediation','community_assistance',
        'health_services','community_programs','infrastructure','barangay_activity'
    ];
    const STATUSES = ['pending','in_progress','resolved','closed'];
}
