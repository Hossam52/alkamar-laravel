<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamResource;
use App\Http\Resources\GradeResource;
use App\Http\Resources\StudentResource;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'group_id' => 'nullable|exists:groups,id'
        ]);
        $exam = Exam::where('id', $request->exam_id)->first();
        $maxGrade = $exam->max_grade;
        if (isset($request->division))
            $division = $request->division % $maxGrade;
        else
            $division = 4;
        if ($division == 0)
            $division = 1;
        $arr = [];
        $percentile = (int) ($maxGrade / $division);
        for ($i = 0; $i < $division; $i++) {
            $from = $i * $percentile;
            $to = $from + $percentile;
            if ($i == $division - 1)
                $to = $maxGrade;
            $arr[] = [
                'from' => $from,
                'to' => $to,
                'count' => 0,
                'students' => []
            ];
        }
        $total = 0;
        $students = Student::byStage($exam->stage_id)
            ->with([
                'grades' => function ($query) use ($exam, $request) {
                    $query->where('exam_id', '=', $exam->id);
                    if (isset($request->group_id))
                        $query->where('group_id', '=', $request->group_id);
                }
            ])
            // ->whereHas('grades', function ($query) use ($exam) {
            //     $query->where('exam_id','=', $exam->id);
            //     dd($query);
            // })
            ->select('students.*', \DB::raw('(SELECT CAST(grade AS double) FROM grades WHERE student_id = students.id AND exam_id = ' . $exam->id . ') as max_grade'))
            ->orderBy('code', 'asc')
            ->get()
            ->sortByDesc('max_grade');
        $arr2 = [];
        foreach ($students as $student) {
            if (count($student->grades) == 0)
                continue;
            $grade = $student->grades[0];
            $percent = $grade->grade; // percent($exam->max_grade);

            if ($student->isDisabled())
                continue;
            foreach ($arr as &$percentileRange) {
                if ($percent >= $percentileRange['from'] && $percent <= $percentileRange['to']) {
                    $percentileRange['count']++;
                    $percentileRange['students'][] = array(
                        'student' => new StudentResource($student),
                        'grade' => new GradeResource($grade),
                    );
                    $total++;
                    break;
                }
            }
            unset($percentileRange); // Unset the reference to avoid unintended changes

        }
        // foreach ($grades as $grade) {
        //     $percent = $grade->grade; // percent($exam->max_grade);
        //     $student = $grade->student;
        //     if($student->isDisabled()) continue;
        //     foreach ($arr as &$percentileRange) {
        //         if ($percent >= $percentileRange['from'] && $percent <= $percentileRange['to']) {
        //             $percentileRange['count']++;
        //             $percentileRange['students'][] = array(
        //                 'student' => new StudentResource($student),
        //                 'grade' => new GradeResource($grade),
        //             );
        //             $total++;
        //             break;
        //         }
        //     }
        //     unset($percentileRange); // Unset the reference to avoid unintended changes

        // }

        //********************For calc total students without group_id **********************/
        // $studentsInStage = Student::where('stage_id', $exam->stage_id)->where('student_status', 1);

        // if(isset($request->group_id)){
        //     $studentsInStage->where('group_id',$request->group_id);
        // }

        // $studentsInStage = $studentsInStage->count();

        $groupId = $request->group_id;
        $totalStudentsCount = DB::table('students')
            ->where('stage_id', $exam->stage_id)->where('student_status', 1);
        if (isset($groupId)) {
            $totalStudentsCount = $totalStudentsCount->leftJoin('grades', 'students.id', '=', 'grades.student_id')
                ->where('students.group_id', $groupId)
                ->whereNotNull('grades.student_id');
        }
        $totalStudentsCount = $totalStudentsCount->count();

        // Count students from the grades table with the specified group_id and are not found in the students table
        $studentsFromGradesCount = 0;
        if (isset($groupId)) {
            $studentsFromGradesCount = DB::table('grades')
                ->leftJoin('students', 'grades.student_id', '=', 'students.id')
                ->where('students.stage_id', $exam->stage_id)
                ->where('student_status', 1)
                ->where('grades.group_id', $groupId)
                ->whereNull('students.group_id')
                ->count();
        }

        $studentsInStage = $totalStudentsCount + $studentsFromGradesCount;
        $studentsNotTakeExam = $studentsInStage - $total;
        return response()->json([
            'exam_absence_count' => $studentsNotTakeExam,
            'total_students_count' => $total,
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
        $permissions = auth()->user()->getPermissions()['exams'];
        if (isset($permissions) && isset($permissions['create']) && $permissions['create']) {

            $arr = $request->all();
            $arr['created_by'] = $request->user()->id;
            $exam = new Exam($arr);
            $exam->save();
            return response()->json(['exam' => new ExamResource($exam)]);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }


    public function collectiveExams(Request $request)
    {
        $request->validate([
            'exam_ids' => 'required|array|exists:exams,id',
            'stage_id' => 'required|exists:stages,id',
            'title' => 'required',
            'exam_date' => 'required|date'
        ]);
        $sumMaxGrades = Exam::whereIn('id', $request->input('exam_ids'))->sum('max_grade');
        $examIds = $request->input('exam_ids');

        $students = Student::byStage($request->stage_id)->With([
            'grades' => function ($query) use ($examIds) {
                $query->whereIn('exam_id', $examIds);
            }
        ])->get();
        // Calculate the sum of grades for each student
        $results = [];
        try {
            DB::beginTransaction();
            $collectiveExam = Exam::create(
                [
                    'title' => $request->title,
                    'stage_id' => $request->stage_id,
                    'created_by' => $request->user()->id,
                    'exam_date' => $request->exam_date,
                    'max_grade' => $sumMaxGrades,
                ]
            );
            foreach ($students as $student) {
                $sumGrades = $student->grades->sum('grade');
                $grade = Grade::create([
                    'exam_id' => $collectiveExam->id,
                    'student_id' => $student->id,
                    'grade' => $sumGrades,
                ]);

            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return response()->json();
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
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'title' => 'nullable',
            'exam_date' => 'nullable',
            'max_grade' => 'nullable',
        ]);
        $permissions = auth()->user()->getPermissions()['exams'];
        if (isset($permissions) && isset($permissions['update']) && $permissions['update']) {
            $exam = Exam::find($request->exam_id);
            if (isset($request->title))
                $exam->title = $request->title;
            if (isset($request->max_grade))
                $exam->max_grade = $request->max_grade;
            if (isset($request->exam_date))
                $exam->exam_date = $request->exam_date;
            $exam->save();
            return response()->json(['exam' => new ExamResource($exam)]);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate(['exam_id' => 'required|exists:exams,id']);
        $permissions = auth()->user()->getPermissions()['exams'];
        if (isset($permissions) && isset($permissions['create']) && $permissions['create']) {
            $exam = Exam::find($request->exam_id);
            $exam->delete();
            return response()->json(['message' => 'تم حذف الامتحان بنجاح']);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }
}