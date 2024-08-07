<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllStudentsList\AttendanceStudentListResource;
use App\Http\Resources\AllStudentsList\GradesStudentListResource;
use App\Http\Resources\AllStudentsList\HomeworkStudentListResource;
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

        $allStudents = Student::byStage($stage_id)->with('grades')->simplePaginate(100);
        $exams = Exam::where('stage_id', $stage_id)->get();

        return response()->json([
            'students' => GradesStudentListResource::collection($allStudents),
            'exams' => ExamResource::collection($exams),
        ], );
    }
    public function studentAttendancesInStage(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);
        $stage_id = $request->stage_id;

        $allStudents = Student::byStage($stage_id)->with('attendances')->simplePaginate(100);
        $lectures = Lecture::byStageId($stage_id)->get();

        return response()->json(
            [
                'students' => AttendanceStudentListResource::collection($allStudents),
                'lectures' => LectureResource::collection($lectures),
            ],
        );
    }

    public function studentHomeworksInStage(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id'
        ]);
        $stage_id = $request->stage_id;

        $allStudents = Student::byStage($stage_id)->with('homeworks')->simplePaginate(100);
        $lectures = Lecture::byStageId($stage_id)->get();

        return response()->json([
            'students' => HomeworkStudentListResource::collection($allStudents),
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


    public function createStudentBlock(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|integer',
            'group_id' => ['nullable', new ValidGroupForStage($request->stage_id)],
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
        return $student;
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $permissions = auth()->user()->getPermissions()['students'];
        if (isset($permissions) && isset($permissions['create']) && $permissions['create']) {

            $student = $this->createStudentBlock($request);

            return response()->json(['student' => new StudentResource($student),]);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
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
            'group_id' => ['nullable', 'exists:groups,id'],

            'code' => 'unique:students,code,' . $request->student_id,
            'name' => 'string',
            'school' => 'string|nullable|',
            'father_phone' => 'string|nullable',
            'mother_phone' => 'string|nullable',
            'student_phone' => 'string|nullable',
            'whatsapp' => 'string|nullable',
            'address' => 'string|nullable',
            'prblems' => 'string|nullable',
            'student_status' => 'boolean|nullable'
        ]);
        $student = Student::find($request->student_id);
        // Update the student's attributes only if they are present in the request
        $fillableAttributes = ['code', 'group_id', 'name', 'school', 'father_phone', 'mother_phone', 'student_phone', 'whatsapp', 'address', 'problems', 'student_status'];

        foreach ($fillableAttributes as $attribute) {
            if ($request->has($attribute)) {
                $student->$attribute = $request->input($attribute);
            }
        }
        $permissions = auth()->user()->getPermissions()['students'];
        if (isset($permissions) && isset($permissions['update']) && $permissions['update']) {
            // Save the updated student
            $student->save();

            return response()->json(
                ['student' => new StudentResource($student)]
            );
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }

    public function generateUniqueCode($initialCode)
    {
        $code = $initialCode;
        while (Student::where('code', $code)->exists()) {
            $code .= '0';
        }
        return $code;
    }

    public function getMinMaxCodeInStage($stage_id){
        $codes = Student::where('stage_id', $stage_id)
        ->select('code')
        ->get()
        ->pluck('code')
        ->filter(function ($code) {
            return is_numeric($code);
        })
        ->map(function ($code) {
            return intval($code);
        });
        $maxCode = $codes->max();
        $minCode = $codes->min();
        return ['max'=>$maxCode,'min'=>$minCode];

    }
    public function createEmptyStudents(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'count' => 'required|numeric|min:1|max:300',
        ]);

        try {
            \DB::beginTransaction();

            $minMaxCodes = $this->getMinMaxCodeInStage($request->stage_id);
            $maxCode = $minMaxCodes['max'];
            $minCode = $minMaxCodes['min'];
            $newCode = $maxCode;
            if($newCode==null)$newCode = $minCode = Student::max('code');
            if (Student::where('code', $newCode + 1)->exists()) {
                $newCode = $this->generateUniqueCode($minCode);
            }
            $assignedCodes = [];
            for ($i = 1; $i <= $request->count; $i++) {



                $studentRequest = $request->merge([
                    'code' => $newCode + $i,
                    'name' => '.',
                    'gender' => 'male',
                    'student_status'=>false
                    // Add other fields as needed
                ]);

                $student = $this->createStudentBlock($studentRequest);
                $assignedCodes[] = $newCode+$i;


            }
            \DB::commit();
            return response()->json(['message' => 'تم تسجيل الطلاب بنجاح', 'codes' => $assignedCodes]);

            //code...
        } catch (\Throwable $th) {
            //throw $th;      
            \DB::rollBack();
            return response()->json(['message' => 'لقد حدث خطأ برجاء المحاولة مرة اخري \n ' . $th->getMessage()], 400);


        }
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