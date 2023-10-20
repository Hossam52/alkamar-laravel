<?php

namespace App\Models;

use App\Models\Group\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //attend_status 1 for attend 2 late 3 for forgot the book
    use HasFactory;
    public function lecture(){
        return $this->belongsTo(Lecture::class,'lec_id');
    }
    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function group(){
        return $this->belongsTo(Group::class,'attend_group_id','id');
    }
    public function scopeByAttend($query){
        return $this->scopeByAttendStatus($query,1);
    }
    public function scopeByLateAttend($query){
        return $this->scopeByAttendStatus($query,2);
    }
    public function scopeByForgot($query){
        return $this->scopeByAttendStatus($query,3);
    }
    public function scopeByStudentId($query,$studentId){
        if(isset($studentId))
            return $query->where('student_id',$studentId);
        return $query;
    }
    public function scopeByAttendStatus($query,$attendStatus){
        if(isset($attendStatus))
            return $query->where('attend_status',$attendStatus);
        return $query;
    }
    
    public function scopeByStudentStatus($query,$studentIds){
        if(isset($studentIds))
            return $query->whereNotIn('student_id',$studentIds);
        return $query;
    }

    public function scopeByLectureId($query,$lec_id){
        if($lec_id)
            return $query->where('lec_id',$lec_id);
        return $query;
    }

    public function scopeByStudentsScanned($query,$lec_id, $assistant_id){
        return $query->byLectureId($lec_id)->where('assistant_id',$assistant_id);
    }

   
    protected $fillable = [
        'id',
        'student_id',
        'assistant_id',
        'lec_id',
        'attend_status',
        'attend_group_id',
    ];
    
    protected $casts = [
        'student_id'=>'integer',
        'assistant_id'=>'integer',
        'lec_id'=>'integer',
        'attend_status'=>'integer',
    ];
}