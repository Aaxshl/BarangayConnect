<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model {
    use HasFactory;
    protected $fillable = [
        'title','body','image','announcement_type','status','published_at',
        'archived_at','created_by'
    ];
    protected $casts = ['published_at' => 'datetime','archived_at' => 'datetime'];

    public function createdBy() { return $this->belongsTo(User::class,'created_by'); }
    const TYPES = ['community_event','health_advisory','public_advisory','emergency_notice','general'];
}
