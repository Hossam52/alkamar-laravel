<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payments\PaymentLookup;
use App\Models\Payments\StudentPayment;
use App\Models\Stages\Stage;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'stage_id'=>'required|exists:stages,id',
            'title'=>'required',
            'month'=>'required|integer',
            'year'=>'required|integer',
        ]);
        $payment = PaymentLookup::byStage($request->stage_id)->where('month',$request->month)->where('year',$request->year)->first();
        if(isset($payment)){
            return response()->json([
                'message'=>'يوجد مصاريف مضافة من قبل الي هذا التاريخ'
            ],400);
        }
        $arr = $request->all();
        $arr['created_by'] = $request->user()->id;
         $payment =  PaymentLookup::create($request->all());
        return response()->json([
            'payment'=>$payment,
        ]);


    }
    public function payment_stats(Request $request){
        $request->validate([
                'payment_id'=>'required|exists:payment_lookups,id'
            ]);
            $payment = PaymentLookup::find($request->payment_id);
            $stage = Stage::find($payment->stage_id);
            $students = $stage->students()->byEnabled()-> pluck('id');
            $disabled = $stage->students()->byDisabled()->count();

            $paidPayment = $payment->studentPayments()->byPaid($students)->count();
            $payLatePayment = $payment->studentPayments()->byLatePaid($students)->count();
            $notPaidPayment = $payment->studentPayments()->byNotPaid($students)->count();
            
            $notAssigned = count($students)-($paidPayment+$notPaidPayment+$payLatePayment);
            
            $totalPaid = $paidPayment + $payLatePayment;
        return response()->json([
            'paid'=>$paidPayment,
            'not_paid'=>$notPaidPayment,
            'late_paid'=>$payLatePayment,
            'not_assigned'=>$notAssigned,
            'disabled'=>$disabled,
            'total_students'=>count($students),
            'total_paid'=>$totalPaid,
        ]);
        
        
    }
    /**
     * Display the specified resource.
     */
    public function show(PaymentLookup $payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentLookup $payments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentLookup $payments)
    {
        //
    }
}
