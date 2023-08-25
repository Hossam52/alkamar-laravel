<?php

namespace App\Http\Resources;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attendance = $this->resource;
        $attendance = Attendance::make($attendance->toArray());
        $group = $attendance->group()->first();
        
        $lecture = $attendance->lecture()->first();
        if (isset($attendance->lecture)) {
            $lecture = $attendance->lecture()->first();
            return (
                [
                    'id' => $attendance->id,
                    'stage_id' => $lecture->stage_id,
                    'title' => $lecture->title,
                    'lecture_date' => $lecture->lecture_date,
                    'attendance_id' => $attendance->id,
                    'student_id' => $attendance->student_id,
                    'attend_status' => $attendance->attend_status,
                    'lec_id' => $attendance->lec_id,
                    'group' => new GroupResource($group)
                ]
            );

        }
        return parent::toArray($request);
    }
}