<?php

namespace App\Http\Resources\AllStudentsList;

use App\Http\Resources\Payments\StudentPaymentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentStudentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {  $data = parent::toArray($request);
        $payments = $this->resource->payments;
        if ($payments) {
            $data['payments'] = StudentPaymentResource::collection($payments);
        }
        return $data;
    }
}
