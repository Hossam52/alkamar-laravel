<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //attend_status 1 for attend 2 late 3 for forgot the book
    use HasFactory;
    public function lecture(){
        return $this->belongsTo(Lecture::class,'lec_id');
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
        'student_id',
        'assistant_id',
        'lec_id',
        'attend_status',
    ];
    
    protected $casts = [
        'student_id'=>'integer',
        'assistant_id'=>'integer',
        'lec_id'=>'integer',
        'attend_status'=>'integer',
    ];
}