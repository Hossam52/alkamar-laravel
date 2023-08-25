<?php

namespace App\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllStudentWithGradesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        return $data;
        // $attributes = [
        //     'id' => $data['id'],
        //     'code' => $data['code'],
        //     'name' => $data['name'],
        // ];
    
        // if (isset($data['attendances'])) {
        //     $attributes['attendances'] = $data['attendances'];
        // }
    
        // if (isset($data['homeworks'])) {
        //     $attributes['homeworks'] = $data['homeworks'];
        // }
    
        // if (isset($data['payments'])) {
        //     $attributes['payments'] = $data['payments'];
        // }
    
        // if (isset($data['grades'])) {
        //     $attributes['grades'] = $data['grades'];
        // }
    
        // return $attributes;
    }
}
