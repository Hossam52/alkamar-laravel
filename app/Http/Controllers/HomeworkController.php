<?php

namespace App\Http\Controllers;

use App\Http\Resources\HomeworkResource;
use App\Models\Homework;
use App\Models\Lecture;
use App\Models\Student;
use Illuminate\Http\Request;

class HomeworkController extends Controller
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
            'lec_id' => 'required|exists:lectures,id',
            'homework_status' => 'required|integer'
        ]);
        $attendance = Homework::byLectureId($request->lec_id) ->where('student_id',$request->student_id)->first();

        if($attendance){
            return response()->json(['message'=>'تم تسجيل هذا الطالب من قبل في هذه المحاضرة'],400);
        }
        
        $lec = Lecture::find($request->lec_id);
        $std = Student::find($request->student_id);

        if($lec->stage_id !=$std->stage_id){
            return response()->json(['message'=>'هذا الطالب غير مسجل في تلك المرحلة'],400);
        }

        $arr = $request->all();
        $arr['assistant_id'] = $request->user()->id;
        $homeworkRecord = new Homework($arr);
        $homeworkRecord->save();

        return response()->json([
            'message' => 'تم تسجيل الواجب بنجاح',          
            'homework' => new HomeworkResource($homeworkRecord)
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(homework $homework)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(homework $homework)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, homework $homework)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(homework $homework)
    {
        //
    }
}

