<?php

namespace App\Models\Payments;

use App\Models\Payments\PaymentLookup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function payment(){
        return $this->belongsTo(PaymentLookup::class);
    }
    
    public function scopeByActive($query){
        $studentPayment =  $query->first();
        if(isset($studentPayment))
            return $studentPayment->payment();

    }

    public function scopeByStudentID($query,$studentId){
        if(isset($studentId)){
            return $query->where('student_id',$studentId);
        }   
        return $query;
    }
    public function scopeByPaidStatus($query){
        return $query->whereIn('payment_status',array(1,2));
    }
    public function scopeByPaymentId($query,$paymentId){
        if(isset($paymentId)){
            return $query->where('payment_id',$paymentId);
        }   
        return $query;
    }
    private  function scopeByStudentStatus($query,$students){
        if(isset($students)){
            return $query->whereIn('student_id',$students);
        }   
        return $query;
    }
    public function scopeByPaid($query,$studentIds){
        return $this-> scopeByPaymentStatus($query,1,$studentIds);
    }
    public function scopeByLatePaid($query,$studentIds){
        return $this-> scopeByPaymentStatus($query,2,$studentIds);
    }
    public function scopeByNotPaid($query,$studentIds){
        return $this-> scopeByPaymentStatus($query,3,$studentIds);
    }
    private function scopeByPaymentStatus($query,$payment_status,$studentIds){
        if(isset($payment_status)){
            return $query->whereIn('student_id',$studentIds)-> where('payment_status',$payment_status);
        }   
        return $query;
    }
    protected $casts = [
        "student_id" => 'integer',
        "payment_id" => 'integer',
        "payment_status" => 'integer',
    ];
}