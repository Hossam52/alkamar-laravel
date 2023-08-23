<?php

namespace App\Models\Group;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group\LectureGroup;
use App\Models\Lecture;

class Group extends Model
{
    use HasFactory;
    public $timestamps = true;

    public function lectures(){
        return $this->belongsToMany(Lecture::class,'groups_lectures','group_id','lec_id');
    }
    public function students(){
        return $this->hasMany(Student::class);
    }
    public function attendances(){
        return $this->hasMany(Attendance::class,'attend_group_id');
    }
    
    public function scopeByStageId($query,int $stage_id){
        if(isset($stage_id)){
            return $query->where('stage_id',$stage_id);
        }
        return $query;
    }
    
    
    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'stage_id',
        'pivot',
    ];
    protected $casts = [
        'stage_id'=>'integer',
        'created_by'=>'integer',
    ];
}
