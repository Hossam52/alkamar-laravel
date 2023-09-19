<?php

namespace App\Http\Resources\AllStudentsList;

use App\Http\Resources\GradeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradesStudentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $grades = $this->resource->grades;
        if($grades){
            $data ['grades'] = GradeResource::collection($grades);
        }  
        return $data;    }
}
