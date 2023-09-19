<?php

namespace App\Http\Resources;

use App\Http\Resources\Payments\StudentPaymentResource;
use App\Http\Resources\Stages\StageResource;
use App\Models\Payments\PaymentLookup;
use App\Models\Payments\StudentPayment;
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
        $group = $student->group()->first();
        // $last_payment = $student->payments()->first();
        $last_payment = PaymentLookup::byStage($stage->id)->orderByDesc('id')->first();

        if ($last_payment !== null) {
            $last_payment = $last_payment->studentPayments()->byStudentID($student->id)->first();
        }
        $grades = $student->grades;
        $data = parent::toArray($request);
        $data['stage'] = $stage->title;
        $data['last_payment'] = new StudentPaymentResource($last_payment);
        if($group){
            $data['group_title'] = $group->title;
            $data['group_id'] = $group->id;

        }
        if($grades){
            $data['grades'] = GradeResource::collection($grades);
        }

        return $data;
    }
}