<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;
    
    public function attedances(){
        return $this->hasMany(Attendance::class,'lec_id');
    }
    
    protected $hidden =[
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'created_by',
        'stage_id',
        'title',
        'lecture_date',
    ];
    protected $casts =[
        'created_by'=>'integer',
        'stage_id'=>'integer',
        'lecture_date'=>'date',
    ];
}