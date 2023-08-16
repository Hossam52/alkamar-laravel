<?php

namespace App\Http\Controllers;

use App\Http\Resources\LectureResource;
use App\Models\Attendance;
use App\Models\Lecture;
use App\Models\Student;
use Illuminate\Http\Request;

class LectureController extends Controller
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
            'stage_id' => 'required|exists:stages,id',
            'title' => 'required',
            'lecture_date' => 'required|date'
        ]);
        $arr = $request->all();
        $arr['created_by'] = $request->user()->id;
        $lec = new Lecture($arr);
        $lec->save();

        return response()->json([
            'message' => 'تم اضافة محاضرة جديدة بنجاح',
            'lecture' => new LectureResource($lec),
        ], 201);
    }
    public function lectureStats(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
        ]);
        $lec = Lecture::find( $request->lecture_id);
        
        $totalStudentsCount = Student::byStage($lec->stage_id)->byStatus(true)-> count();
        
        $studentsDiabled = Student::byStage($lec->stage_id)->byStatus(false)-> pluck('id')->toArray();

        $lectureAttendances = $lec->attedances()->byStudentStatus($studentsDiabled)->count();

        $attends = $lec->attedances()->byAttendStatus(1)->byStudentStatus($studentsDiabled)-> count(); //For attended students
        $late = $lec->attedances()->byAttendStatus(2)->byStudentStatus($studentsDiabled)-> count(); //For late students
        $forgot = $lec->attedances()->byAttendStatus(3)->byStudentStatus($studentsDiabled)-> count(); //For forgot book
        
        $abscence = $totalStudentsCount - $lectureAttendances;
        
        return response()->json([
            'total_attendance_count' => $lectureAttendances,
            'attends_count' => $attends,
            'late_count' => $late,
            'forgot_book_count' => $forgot,
            'absence_count' => $abscence,
            'students_count' => $totalStudentsCount,
            'disabled_count' => count($studentsDiabled),
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Lecture $lecture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lecture $lecture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lecture $lecture)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'title' => 'nullable',
            'lecture_date' => 'nullable'
        ]);

        $lecture = Lecture::find($request->lecture_id);
        if(isset($request->title)) $lecture->title = $request->title;
        if(isset($request->lecture_date)) $lecture->lecture_date = $request->lecture_date;
        $lecture->save();

        return response()->json(['lecture'=>new LectureResource($lecture)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate(['lecture_id'=>'required|exists:lectures,id']);
        $lecture = Lecture::find($request->lecture_id);
        $lecture->delete();
        return response()->json(['message'=>'تم حذف المحاضرة بنجاح']);
    }
}