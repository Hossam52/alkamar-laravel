<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Http\Resources\GradeResource;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function allExams(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);
        $exams = Exam::where('stage_id', $request->stage_id)->get();
        return response()->json([
            'exams' => ExamResource::collection($exams),
        ]);
    }
    public function examStatistics(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id|integer',
            'division' => 'nullable|max:100',
        ]);
        if (isset($request->division))
            $division = $request->division % 100;
        else
            $division = 4;
        if ($division == 0)
            $division = 1;
        $exam = Exam::where('id', $request->exam_id)->first();
        $grades = $exam->grades()->get();
        $arr = [];
        $percentile = (int) (100 / $division);
        for ($i = 0; $i < $division; $i++) {
            $from = $i * $percentile;
            $to = $from + $percentile;
            if ($i == $division - 1)
                $to = 100;
            $arr[] = [
                'from' => $from,
                'to' => $to,
                'count' => 0,
            ];
        }
        $total = 0;

        foreach ($grades as $grade) {
            $found = false;
            $percent = $grade->percent($exam->max_grade);
            foreach ($arr as &$percentileRange) {
                if ($percent >= $percentileRange['from'] && $percent <= $percentileRange['to']) {
                    $percentileRange['count']++;
                    $total++;
                    $found = true;
                    break;
                }
            }
            unset($percentileRange); // Unset the reference to avoid unintended changes

        }
        $studentsInStage = Student::where('stage_id', $exam->stage_id)->count();
        $studentsNotTakeExam = $studentsInStage - $grades->count();
        return response()->json([
            'exam_absence_count' => $studentsNotTakeExam,
            'total_students_count' => $grades->count(),
            'stats' => $arr,
        ]);
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
            'max_grade' => 'required|integer',
            'exam_date' => 'required|date'
        ]);
        $arr = $request->all();
        $arr['created_by'] = $request->user()->id;
        $exam = new Exam($arr);
        $exam->save();
        return response()->json(['exam' => new ExamResource($exam)]);
    }
    public function collectiveExams()
    {
        $exams = Grade::all()->sortBy('exam_id')->groupBy('student_id');
        return response()->json([
            'exams'=>$exams,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        //
    }
}