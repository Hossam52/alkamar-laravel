<?php

namespace App\Models\Stages;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;
    public function stageType(){
        return $this->belongsTo(StageType::class);
    }
    public function students(){
        return $this->hasMany(Student::class);
    }
    protected $hidden =[
        'created_at',
        'updated_at',
    ];
}
