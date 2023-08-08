<?php

namespace App\Http\Resources;

use App\Http\Resources\Stages\StageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $student = $this->resource;
        $stage = $student->stage()->first();
        $grades = $student->grades()->get();
        $data = parent::toArray($request);
        $data['stage'] = $stage->title ;
        // $data['stage'] = new StageResource($stage);
        // $data['grades'] =  GradeResource::collection($grades);
        return $data;
    }
}
