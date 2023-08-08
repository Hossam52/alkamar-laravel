<?php

namespace App\Http\Resources\Stages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StageTypeResource extends JsonResource
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
        return $types;
    }
}
