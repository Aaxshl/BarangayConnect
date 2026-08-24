<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Household extends Model {
    use HasFactory;
    protected $fillable = [
        'household_id','head_resident_id','address','purok','zone',
        'contact_number','number_of_members','status'
    ];
    public function head() { return $this->belongsTo(Resident::class,'head_resident_id'); }
    public function members() { return $this->hasMany(Resident::class); }
    public function getMemberCountAttribute() { return $this->members()->count(); }
}
