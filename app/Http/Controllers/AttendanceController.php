<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Homework;
use App\Models\Lecture;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'attend_group_id' => 'required|exists:groups,id',
            'lec_id' => 'required|exists:lectures,id',
            'attend_status' => 'integer|nullable'
        ]);
        
        $attendance = Attendance::byLectureId($request->lec_id) ->where('student_id',$request->student_id)->first();
        $homework = Homework::byLectureId($request->lec_id)->where('student_id',$request->student_id)->first();
       
        if($attendance){
            if(!$request->has('attend_status')){
                if($homework)$homework->delete();
                $attendance->delete();
                return response()->json(['message'=>'تم الغاء حضور الطالب بنجاح']);
            }
            return response()->json(['message'=>'تم تسجيل هذا الطالب من قبل في هذه المحاضرة'],400);
        }
        
        $lec = Lecture::where('id',$request->lec_id)->first();
        $std = Student::where('id',$request->student_id)->first();
       
      
        if($std->isDisabled()){
            return response()->json(['message'=>'هذا الطالب متوقف يجب جعله منتظم اولا'],400);
        }
        if($lec->stage_id !=$std->stage_id){
            return response()->json(['message'=>'هذا الطالب غير مسجل في تلك المرحلة'],400);
        }
        if(!$request->has('attend_status')){
            return response()->json([
                'message'=>'هذا الطالب لم يتم تسجيل حضوره من قبل'
            ],400);
        }
        $arr = $request->all();
        $arr['assistant_id'] = $request->user()->id;
        $attendance_record = new Attendance($arr);
        $attendance_record->save();
        
       //Make  default when attend student is to make homework to be done
        $homework = new Homework([
            'student_id'=>$std->id,
            'assistant_id'=>$request->user()->id,
            'lec_id'=>$lec->id,
            'homework_status'=>1,
        ]);
        $homework->save();
       

        $studentIdsByAssistant = Attendance::byStudentsScanned($request->lec_id,$request->user()->id)->get(['student_id']);
        $maleStds = Student::byMaleCount($studentIdsByAssistant)->count();
        $femaleStds = Student::byFemaleCount($studentIdsByAssistant)->count();

        return response()->json([
            'message' => 'تم تسجيل الحضور بنجاح',
            'male'=>$maleStds,
            'female'=>$femaleStds,
            'attendance' => new AttendanceResource($attendance_record)
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}