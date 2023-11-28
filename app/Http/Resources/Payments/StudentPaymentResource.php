<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payment = $this->resource;
        $data = parent::toArray($request);

        if (isset($payment->payment)) {
            $paymentLookup = $payment->payment;
            return (
                [
                    'id' => $payment->id,
                    'stage_id' => $paymentLookup->stage_id,
                    'title' => $paymentLookup->title,
                    'status' => $paymentLookup->status,
                    'price' => $paymentLookup->price,
                    'month' => $paymentLookup->month,
                    'year' => $paymentLookup->year,
                    'student_id' => $payment->student_id,
                    'payment_status' => $payment->payment_status,
                    'payment_id' => $payment->payment_id,
                    'created_at' => $payment->created_at,
                  
                ]
            );

        }
        return $data;
    }
}
