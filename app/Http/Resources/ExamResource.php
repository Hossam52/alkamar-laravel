<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Cast stage_id and max_grade fields to integers
        $data['stage_id'] = (int)$data['stage_id'];
        $data['max_grade'] = (int)$data['max_grade'];
        if(isset($data['grade']))$data['grade'] = (float)$data['grade'];
    
        return $data;    }
}
