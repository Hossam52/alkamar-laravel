<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllStudentWithGradesResource;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\GradeResource;
use App\Http\Resources\HomeworkResource;
use App\Http\Resources\LectureResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Homework;
use App\Models\Lecture;
use App\Models\Stages\Stage;
use App\Models\Student;
use App\Rules\ValidGroupForStage;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function studentExamsInStage(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);


        $stage_id = $request->stage_id;

        $students = Student::byStage($stage_id)->get();
        $allStudents = $students->map(function ($student) {

            $res = $student->studentAllExamGrades()->get();
            $grades = ExamResource::collection($res);

            $student['grades'] = $grades;
            return $student;
        });

        $males = Student::byStage($stage_id)->byMale()->get();
        $females = Student::byStage($stage_id)->byFemale()->get();

        $exams = Exam::where('stage_id', $stage_id)->get();

        return response()->json([
            'students' => AllStudentWithGradesResource::collection($allStudents),
            'exams' => ExamResource::collection($exams),
        ], );
    }
    public function studentAttendancesInStage(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);
        
        
        $stage_id = $request->stage_id;
        
        $lectures = Lecture::byStageId($stage_id)->get();

        $totalStudents = Student::byStage($stage_id)->count();
        $students = Student::byStage($stage_id)->get();//simplePaginate(100);
        $allStudents = $students->map(function ($student) {
            $res = $student->attendances()->get();
            $attendances = AttendanceResource::collection($res);
            $student['attendances'] = $attendances;
            return $student;
        });

        return response()->json([
            'total_students' => $totalStudents,
            'students' => AllStudentWithGradesResource::collection($allStudents),
            'lectures' => LectureResource::collection($lectures),
        ], );
    }
    
    public function studentHomeworksInStage(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);


        $stage_id = $request->stage_id;

        $students = Student::byStage($stage_id)->get();
        $allStudents = $students->map(function ($student) {

            $res = $student->homeworks()->get();
            $homeworks = HomeworkResource::collection($res);

            $student['homeworks'] = $homeworks;
            return $student;
        });

          $lectures = Lecture::where('stage_id', $stage_id)->get();

        return response()->json([
            'students' => AllStudentWithGradesResource::collection($allStudents),
            'lectures' => LectureResource::collection($lectures),
        ], );
    }

    public function studentProfile(Request $request)
    {
        $request->validate([
            'student_id' => 'required_without:student_code|exists:students,id|nullable',
            'student_code' => 'required_without:student_id|exists:students,code|nullable',
        ]);

        $student = null;

        if ($request->has('student_id')) {
            $student = Student::where('id', $request->student_id)->first();
        } elseif ($request->has('student_code')) {
            $student = Student::where('code', $request->student_code)->first();
        }

        if ($student) {
            return response()->json(['student' => new StudentResource($student)]);
        } else {
            return response()->json(['message' => 'Student not found'], 404);
        }
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
            'stage_id' => 'required|integer',
            'group_id'=>['nullable',new ValidGroupForStage($request->stage_id)],
            'code' => 'required|unique:students,code',
            'name' => 'required',
            'school' => 'string|nullable',
            'father_phone' => 'string|nullable',
            'mother_phone' => 'string|nullable',
            'student_phone' => 'string|nullable',
            'whatsapp' => 'string|nullable',
            'address' => 'string|nullable',
            'gender' => 'required',
            'problems' => 'string|nullable',
            'student_status' => 'boolean|nullable',
        ]);

        $studentData = $request->all();
        $studentData['created_by'] = $request->user()->id;
        $student = new Student($studentData);
        $student->save();

        $student->saveQr();

        return response()->json(['student' => new StudentResource($student),]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Student $student)
    {
        //
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $studentId = $request->student_id;
        $student = Student::where('id', $studentId)->first();
        $grades = Grade::where('student_id', $studentId)->get();
        $late = Attendance::where('student_id', $studentId)->where('attend_status', 2)->get();

        $allAttendances = Attendance::where('student_id', $studentId)->get(['lec_id']);
        $allAbsense = Lecture::where('stage_id', $student->stage_id)->where('lecture_date', '>=', \Carbon\Carbon::parse($student->created_at)->format('Y/m/d'))->whereNotIn('id', $allAttendances)->get();
        $allHomeworks = Homework::where('student_id', $studentId)->get();


        return response()->json([
            'student' => new StudentResource($student),
            'grades' => GradeResource::collection($grades),
            'attendance_late' => AttendanceResource::collection($late),
            'absence' => LectureResource::collection($allAbsense),
            'homeworks' => HomeworkResource::collection($allHomeworks),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'group_id'=>['nullable','exists:groups,id'],

            'code' => 'unique:students,code,' . $request->student_id,
            'name' => 'string',
            'school' => 'string|nullable|',
            'father_phone' => 'string|nullable',
            'mother_phone' => 'string|nullable',
            'student_phone' => 'string|nullable',
            'whatsapp' => 'string|nullable',
            'address' => 'string|nullable',
            'prblems'=>'string|nullable',
            'student_status'=>'boolean|nullable'
        ]);
        $student = Student::find($request->student_id);
        // Update the student's attributes only if they are present in the request
        $fillableAttributes = ['code','group_id', 'name', 'school', 'father_phone', 'mother_phone', 'student_phone', 'whatsapp', 'address','problems','student_status'];

        foreach ($fillableAttributes as $attribute) {
            if ($request->has($attribute)) {
                $student->$attribute = $request->input($attribute);
            }
        }

        // Save the updated student
        $student->save();

        return response()->json(
            ['student' => new StudentResource($student)]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
/*
// Retrieve all students along with their grades (if available)
    $students = Student::with(['grades' => function ($query) {
            $query->select('exam_id', 'student_id', 'grade');
        }])
        ->get();

    // Retrieve all exams
    $exams = Exam::all();

    // Format the response to include all exams and the student's grade for each exam
    $response = $students->map(function ($student) use ($exams) {
        $grades = $exams->mapWithKeys(function ($exam) use ($student) {
            $grade = $student->grades->firstWhere('exam_id', $exam->id);

            return [$exam->id => $grade ? $grade->grade : null];
        });

        return [
            'id' => $student->id,
            'name' => $student->name,
            'grades' => $grades,
        ];
    });

    return response()->json([
        'students' => $response,
    ]);
 
 */