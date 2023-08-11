<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $homework = $this->resource;
        $data = parent::toArray($request);

        if (isset($homework->lecture)) {
            $lecture = $homework->lecture()->first();
            return (
                [
                    'id' => $homework->id,
                    'stage_id' => $lecture->stage_id,
                    'title' => $lecture->title,
                    'lecture_date' => $lecture->lecture_date,
                    'student_id' => $homework->student_id,
                    'homework_status' => $homework->homework_status,
                    'lec_id' => $homework->lec_id,
                ]
            );

        }
        return $data;
    }
}
