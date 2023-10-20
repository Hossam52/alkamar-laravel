<?php

namespace App\Http\Resources;

use App\Http\Resources\AttendanceResource;
use App\Http\Resources\Payments\StudentPaymentResource;
use App\Http\Resources\Stages\StageResource;
use App\Models\Attendance;
use App\Models\Lecture;
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
        // $last_payments = $student->payments()->byPaidStatus()-> orderBy('payment_id')->take(3)->get();
        
        $lastLecture = Lecture::byStageId($student->stage_id)->orderByDesc('created_at')->skip(1)->take(1)->first();
        $lastAttendance = $student->attendances()->where('lec_id',$lastLecture->id)->first(); 
       
        $lastPayment = PaymentLookup::byStage($student->stage_id)->orderByDesc('id')->skip(1)->take(1)->first();
        $lastMonthPayment = $student->payments()->where('payment_id',$lastPayment->id)->first(); 
        
        $currentMonthPayment = PaymentLookup::byStage($stage->id)->orderByDesc('id')->first();

        if ($currentMonthPayment !== null) {
            $currentMonthPayment = $currentMonthPayment->studentPayments()->byStudentID($student->id)->first();
        }
        $grades = $student->grades;
        $data = parent::toArray($request);
        $data['stage'] = $stage->title;
        $data['current_month_payment'] =  new StudentPaymentResource($currentMonthPayment);
        $data['last_month_payment'] =  new StudentPaymentResource($lastMonthPayment);
        // $data['last_payments'] =  StudentPaymentResource::collection($last_payments);

        $data['last_attendance'] = new AttendanceResource($lastAttendance);
        
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