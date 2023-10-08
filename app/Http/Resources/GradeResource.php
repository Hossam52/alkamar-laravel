<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        $grade = $this->resource;
        $exam = $grade->exam()->first();

        $data = parent::toArray($request);
        return ([
            'id'=>$grade->id,
            'stage_id'=>$exam->stage_id,
            'title'=>$exam->title,
            'max_grade'=>$exam->max_grade,
            'exam_date'=>$exam->exam_date,
            'grade_id'=>$grade->id,
            'group_id'=>$grade->group_id,
            'student_id'=>$grade->student_id,
            'grade'=>$grade->grade,
            'exam_id'=>$grade->exam_id,
        ]);
        $data['title'] =$exam->title;
        $data['max_grade'] = $exam->max_grade;

        
        $data['grade_percent'] = min([100,$grade->grade/$exam->max_grade*100]);

        
        return $data;
    }
}
