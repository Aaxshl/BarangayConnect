<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CitizenRequest extends Model {
    use HasFactory;
    protected $fillable = [
        'tracking_number','resident_id','request_type','description',
        'location','latitude','longitude','photo','status','assigned_to',
        'resolved_at','resolution_note','notified_at','viewed_at'
    ];
    protected $casts = ['resolved_at' => 'datetime','notified_at' => 'datetime','viewed_at' => 'datetime'];

    public function resident() { return $this->belongsTo(Resident::class); }
    public function assignedTo() { return $this->belongsTo(User::class,'assigned_to'); }

    const TYPES = [
        'broken_streetlight','garbage_collection','illegal_dumping','road_damage',
        'clogged_drainage','flooding','noise_complaint','stray_animal','public_safety'
    ];
    const STATUSES = ['pending','under_review','assigned','in_progress','resolved','closed'];

    public static function generateTracking() {
        $year = date('Y');
        $last = self::whereYear('created_at',$year)->count() + 1;
        return 'REQ-'.$year.'-'.str_pad($last,4,'0',STR_PAD_LEFT);
    }
}
