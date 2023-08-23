<?php

namespace App\Models\Group;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureGroup extends Model
{
    use HasFactory;
    protected $dates = ['created_at','updated_at'];

  
    public function scopeByLectureId($query,int $lec_id){
        if(isset($lec_id)){
            return $query->where('lec_id',$lec_id);
        }
        return $query;
    }

    protected $guarded = [];

}
