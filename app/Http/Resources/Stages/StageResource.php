<?php

namespace App\Http\Resources\Stages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stage = $this->resource;
        $stageType = $stage->stageType()->first();
        $data = parent::toArray($request);
        $data['stage_type'] = $stageType['name'];
        return $data;
    }
}
