<?php

namespace App\Http\Resources\AllStudentsList;

use App\Http\Resources\HomeworkResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkStudentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = parent::toArray($request);
        $homeworks = $this->resource->homeworks;
        if ($homeworks) {
            $data['homeworks'] = HomeworkResource::collection($homeworks);
        }
        return $data;
    }
}