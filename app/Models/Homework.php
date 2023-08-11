<?php

namespace App\Models;

use App\Models\Lecture;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;
    protected $table ='homeworks';
    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function assistant(){
        return $this->belongsTo(User::class);
    }
    public function lecture(){
        return $this->belongsTo(Lecture::class,'lec_id');
    }
    
    public function scopeByLectureId($query,$lec_id){
        if($lec_id)
            return $query->where('lec_id',$lec_id);
        return $query;
    }


    protected $fillable = [
        'student_id',
        'assistant_id',
        'lec_id',
        'homework_status',
    ];
    
    protected $casts = [
        'student_id'=>'integer',
        'assistant_id'=>'integer',
        'lec_id'=>'integer',
        'homework_status'=>'integer',
    ];

}
