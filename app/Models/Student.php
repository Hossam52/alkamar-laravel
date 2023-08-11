<?php

namespace App\Models;

use App\Models\Stages\Stage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Student extends Model
{
    use HasFactory;
    public function stage(){
        return $this->belongsTo(Stage::class);
    }
    public function grades(){
        return $this->hasMany(Grade::class);
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
    public function studentAllExamGrades(){
        $gradeRes = Grade::where('student_id', $this->id)->select('id as grade_id', 'student_id', 'grade', 'exam_id');
        $res = Exam::where('stage_id',$this->stage_id)->leftJoinSub($gradeRes, 'gradeRes', function ($join) {
            $join->on('exams.id', '=', 'gradeRes.exam_id');
        });
        return $res;
    }
    public function studentAllAttendancesGrades(){
        $attendanceRes = Attendance::where('student_id', $this->id)->select('id as attendance_id', 'student_id', 'attend_status', 'lec_id');
        $res = Lecture::where('stage_id',$this->stage_id)->leftJoinSub($attendanceRes, 'attendanceRes', function ($join) {
            $join->on('lectures.id', '=', 'attendanceRes.lec_id');
        });
        return $res;
    }
    public function studentAllHomeworks(){
        $homeworkRes = Homework::where('student_id', $this->id)->select('id as homework_id', 'student_id', 'homework_status', 'lec_id');
        $res = Lecture::where('stage_id',$this->stage_id)->leftJoinSub($homeworkRes, 'homeworkRes', function ($join) {
            $join->on('lectures.id', '=', 'homeworkRes.lec_id');
        });
        return $res;
    }
    public function scopeByStage($query,$stage_id){
        if($stage_id){
            return $query->where('stage_id',$stage_id)->orderBy('code');
        }
        return $query;
    }
    public function scopeByMale($query){
        return $query->where('gender','male');
    }
    public function scopeByFemale($query){
        return $query->where('gender','female');
    }

    public function scopeByMaleCount($query,$studentIdsByAssistant){
        return $query->whereIn('id',$studentIdsByAssistant)->byMale();
    }
    public function scopeByFemaleCount($query,$studentIdsByAssistant){
        return $query->whereIn('id',$studentIdsByAssistant)->byFemale();
    }


    
    public function saveQr(){

        $stage = $this->stage()->first();
   
        // Custom QR code contents
        $contents = $this->id;
    
        $qrCode = QrCode::size(500)  // Set the size of the QR code (in pixels)
        ->backgroundColor(255, 255, 255, 0) // Set transparent background
        ->margin(0)               // Set the margin to zero
        ->errorCorrection('H')    // Set the error correction level (L, M, Q, H)
        ->generate($contents);
    
        // Set the directory to save the PNG file
        $filePath = public_path('qrcodes'. '/'.$stage->id  );
        
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
    protected $hidden = [
        'created_by',
        'updated_at',
    ];
    protected $fillable = [
        'stage_id',
        'created_by',
        'code',
        'name',
        'gender',
        'school',
        'father_phone',
        'mother_phone',
        'student_phone',
        'whatsapp',
        'address',
        'qr_code'
    ];
}
