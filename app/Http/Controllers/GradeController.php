<?php

namespace App\Http\Controllers;

use App\Http\Resources\GradeResource;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
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
            'exam_id'=>'required|exists:exams,id',
            'student_id'=>'required|exists:students,id',
            'grade'=>'required|decimal:0,2'
        ]);
        $std = Student::find($request->student_id);
        if($std->isDisabled()){
            return response()->json(['message'=>'هذا الطالب متوقف يجب جعله منتظم اولا'],400);
        }
     
        $grade = Grade::where('exam_id',$request->exam_id)->where('student_id',$request->student_id)->first();

        // if($grade){
        //     return response()->json(['message'=>'تم تسجيل هذا الطالب من قبل لهذا الامتحان'],400);
        // }
        $exam = Exam::where('id',$request->exam_id)->first();
        if($exam->max_grade < $request->grade)
            return response()->json(['message'=>'يجب ان تكون الدرجة اقل من الدرجة العظمي('.$exam->max_grade.'درجة)'],400);
        
        if($grade){//there is found grade then update it
            $grade->grade = $request->grade;
            $grade->save();
        }else{
            $grade = new Grade($request->all());
            $grade->save();
        }

        return response()->json([
            'message'=>'تم تسجيل الدرجة للطالب',
            'grade'=>new GradeResource($grade)

        ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grades)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grades)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grades)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grades)
    {
        //
    }
}
