<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllStudentsList\PaymentStudentListResource;
use App\Http\Resources\AllStudentWithGradesResource;
use App\Http\Resources\Payments\PaymentLookupResource;
use App\Http\Resources\Payments\StudentPaymentResource;
use App\Models\Payments\PaymentLookup;
use App\Models\Payments\StudentPayment;
use App\Models\Payments\StudentPayments;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);
        $stage_id = $request->stage_id;

        $allStudents = Student::byStage($stage_id)->with('payments')->simplePaginate('100');
        $payments = PaymentLookup::byStage($stage_id)->get();

        return response()->json([
            'students' => PaymentStudentListResource::collection($allStudents),
            'payments' => PaymentLookupResource::collection($payments),
        ], );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'=>'required|exists:students,id',
            'payment_id'=>'required|exists:payment_lookups,id',
            'payment_status'=>'required|in:1,2,3'
        ]);
        $payment = StudentPayment::byStudentID($request->student_id)->byPaymentId($request->payment_id)->first();
        $student = Student::find($request->student_id);
        if($student->isDisabled()){
            return response()->json(['message'=>'هذا الطالب متوقف يجب جعله منتظم اولا'],400);
        }
        if($payment){
            $payment->payment_status = $request->payment_status;
            $payment->save();
            // return response()->json(['message'=>'هذا الطالب قد دفع مسبقا يوم '. Carbon::parse($payment->created_at)->format('Y-m-d')],400);
        }
        else{
            $arr = $request->all();
            $arr['created_by'] = $request->user()->id;
            $payment = StudentPayment::create($arr);
            
        }
        return response()->json(['payment'=>new StudentPaymentResource($payment)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentPayment $studentPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentPayment $studentPayments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentPayment $studentPayments)
    {
        //
    }
}
