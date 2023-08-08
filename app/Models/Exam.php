<?php

namespace App\Models;

use App\Models\Stages\Stage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    public function stage(){
        return $this->belongsTo(Stage::class);
    }
    public function grades(){
        return $this->hasMany(Grade::class);
    }

    protected $hidden =[
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $fillable =[
        'stage_id',
        'created_by',
        'title',
        'max_grade',
        'exam_date',
    ];
}
