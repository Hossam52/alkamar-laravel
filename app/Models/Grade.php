<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function percent(int $examGrade){
        return (double)($this->grade / $examGrade)*100;
    }
    protected $hidden =[
        'student_id',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'exam_id',
        'student_id',
        'grade',
    ];
    protected $casts = [
    'exam_id'=>'integer',
    'student_id'=>'integer',
    'grade'=>'float'
    ];
}