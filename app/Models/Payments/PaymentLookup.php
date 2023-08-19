<?php

namespace App\Models\Payments;

use App\Models\Payments\StudentPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLookup extends Model
{
    use HasFactory;
    public $timestamps = false;
    public function studentPayments(){
        return $this->hasMany(StudentPayment::class,'payment_id');
    }
    public function scopeByStage($query,$stage_id){
        if(isset($stage_id)){
            return $query->where('stage_id',$stage_id);
        }   
        return $query;
    }


    protected $fillable = [
        'stage_id',
        'created_by',
        'title',
        'status',
        'price',
        'month',
        'year',
    ];
    protected $casts=[
        'stage_id'=>'integer',
        'status'=>'boolean',
        'price'=>'integer',
    ];
}
