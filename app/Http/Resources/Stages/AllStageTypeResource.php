<?php

namespace App\Http\Resources\Stages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllStageTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stageType = $this->resource;
        $types = parent::toArray($request);
        $types['stages'] = StageResource::collection($stageType->stages()->get());
        return $types;    }
}
