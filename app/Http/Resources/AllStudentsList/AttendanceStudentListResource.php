<?php

namespace App\Http\Resources\AllStudentsList;

use App\Http\Resources\AttendanceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceStudentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $attendances = $this->resource->attendances;
        if($attendances){
            $data ['attendances'] = AttendanceResource::collection($attendances);
        }  
        return $data;
    }
}
