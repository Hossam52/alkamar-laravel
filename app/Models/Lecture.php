<?php

namespace App\Models;

use App\Models\Group\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;
    public $timestamps = true;


    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'lec_id');
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_lectures', 'lec_id', 'group_id');
    }

    public function lectureGroups()
    {
        $this->groups()->orderBy('group_id')->get()->toArray();
    }
    public function storeGroup(array $groups){
        $attachData = $groups;

        $this->groups()->syncWithoutDetaching($attachData);
    }
    public function scopeByStageId($query,$stage_id){
        if(isset($stage_id)){
            return $query->where('stage_id',$stage_id);
        }
        return $query;
    }
    protected $hidden = [
        'created_by',

    ];
    protected $fillable = [
        'created_by',
        'stage_id',
        'title',
        'lecture_date',
    ];
    protected $casts = [
        'created_by' => 'integer',
        'stage_id' => 'integer',
        'lecture_date' => 'date',
    ];
}