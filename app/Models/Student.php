<?php

namespace App\Models;

use App\Models\Group\Group;
use App\Models\Payments\PaymentLookup;
use App\Models\Payments\StudentPayment;
use App\Models\Stages\Stage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Student extends Model
{
    use HasFactory;
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
    public function group(){
        return $this->belongsTo(Group::class);
    }
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    public function homeworks(){
        return $this->hasMany(Homework::class);
    }
    public function attendances(){
        return $this->hasMany(Attendance::class);
    }
    public function payments(){
        return $this->hasMany(StudentPayment::class);
    }

    protected $appends = ['qr_code_url'];

    // ... other attributes and methods ...

    public function getQrCodeUrlAttribute()
    {
        $host = Request::getHost();
        $port = Request::getPort();
        $qrPath = "/qrcodes/{$this->stage_id}/{$this->id}.svg";

        return "http://{$host}:{$port}{$qrPath}";
    }
    public function studentAllExamGrades()
    {
        $gradeRes = $this->grades()->select('id as grade_id', 'student_id', 'grade', 'exam_id');
        $res = Exam::where('stage_id', $this->stage_id)->leftJoinSub($gradeRes, 'gradeRes', function ($join) {
            $join->on('exams.id', '=', 'gradeRes.exam_id');
        });
        return $res;
    }    public function scopeByStage($query, $stage_id)
    {
        if ($stage_id) {
            return $query->where('stage_id', $stage_id)->
            orderByRaw("CAST(code AS UNSIGNED)");
        }
        return $query->
        orderByRaw("CAST(code AS UNSIGNED)");
    }
    public function scopeByGroup($query,$group_id){
        if (isset($group_id)) {
            return $query->where('group_id',$group_id);
        }
        return $query;
    }
    public function scopeByDisabled($query){
        return $this->scopeByStatus($query,false);
    }
    public function scopeByEnabled($query){
        return $this->scopeByStatus($query,true);
    }
    public function scopeByStatus($query, $student_status)
    {
        if (isset($student_status)) {
            return $query->where('student_status',$student_status);
        }
        return $query;
    }
    public function scopeByMale($query)
    {
        return $query->where('gender', 'male');
    }
    public function scopeByFemale($query)
    {
        return $query->where('gender', 'female');
    }

    public function scopeByMaleCount($query, $studentIdsByAssistant)
    {
        return $query->whereIn('id', $studentIdsByAssistant)->byMale();
    }
    public function scopeByFemaleCount($query, $studentIdsByAssistant)
    {
        return $query->whereIn('id', $studentIdsByAssistant)->byFemale();
    }



    public function saveQr()
    {

        $stage = $this->stage()->first();

        // Custom QR code contents
        $contents = $this->id;

        $qrCode = QrCode::size(500) // Set the size of the QR code (in pixels)
            ->backgroundColor(255, 255, 255, 0) // Set transparent background
            ->margin(0) // Set the margin to zero
            ->errorCorrection('H') // Set the error correction level (L, M, Q, H)
            ->generate($contents);

        // Set the directory to save the PNG file
        $filePath = public_path('qrcodes' . '/' . $stage->id);

        // Set the filename
        $fileName = $this->id . '.svg';

        // Set the full path
        $fullPath = $filePath . '/' . $fileName;

        // Create the directory if it doesn't exist
        if (!\File::isDirectory($filePath)) {
            \File::makeDirectory($filePath, 0755, true);
        }

        // Save the QR code as an SVG file
        file_put_contents($fullPath, $qrCode);
        $this->qr_code_path = $fullPath;
        $this->save();
    }

    public  function isDisabled():bool{
        return $this->student_status==0;
    }
    protected $hidden = [
        'created_by',
        'updated_at',
    ];
    protected $fillable = [
        'stage_id',
        'group_id',
        'created_by',
        'code',
        'name',
        'gender',
        'problems',
        'student_status',
        'school',
        'father_phone',
        'mother_phone',
        'student_phone',
        'whatsapp',
        'address',
        'qr_code'
    ];

    protected $casts = [
        'student_status' => 'boolean'
    ];
}