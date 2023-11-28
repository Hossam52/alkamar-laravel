<?php

namespace App\Http\Controllers;

use App\Http\Resources\LectureResource;
use App\Models\Attendance;
use App\Models\Group\Group;
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
        $groups = Group::byStageId($request->stage_id)->pluck('id')->toArray();
        $permissions = auth()->user()->getPermissions()['lectures'];
        if (isset($permissions) && isset($permissions['create']) && $permissions['create']) {
            $arr = $request->all();
            $arr['created_by'] = $request->user()->id;
            $lec = new Lecture($arr);
            $lec->save();

            $arr = [];
            foreach ($groups as $group) {
                $arr[$group] = [];
            }
            $lec->storeGroup($arr);

            return response()->json([
                'message' => 'تم اضافة محاضرة جديدة بنجاح',
                'lecture' => new LectureResource($lec),
            ], 201);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }
    public function lecStstsArray($group_title, $lectureAttendances, $attends, $late, $forgot, $abscence, $totalStudentsCount, $studentsDiabled)
    {
        return [
            'group_title' => $group_title,
            'total_attendance_count' => $lectureAttendances,
            'attends_count' => $attends,
            'late_count' => $late,
            'forgot_book_count' => $forgot,
            'absence_count' => $abscence,
            'students_count' => $totalStudentsCount,
            'disabled_count' => count($studentsDiabled),
        ];
    }
    public function lectureStats(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'group_id' => 'array',
            'group_id.*' => 'exists:groups,id',
        ]);
        $arr = [];
        $lec = Lecture::find($request->lecture_id);
        if ($request->has('group_id') && count($request->group_id) != 0) {

            foreach ($request->group_id as $id) {
                $group = Group::find($id);
                $students = $group->students();
                $studentsDiabled = $students->byDisabled()->pluck('id');

                $totalStudentsCount = $group->students()->byEnabled()->count();
                $lectureAttendances = $group->attendances()->byLectureId($lec->id)->byStudentStatus($studentsDiabled)->count();

                $attends = $group->attendances()->byLectureId($lec->id)->byAttend()->byStudentStatus($studentsDiabled)->count(); //For attended students
                $late = $group->attendances()->byLectureId($lec->id)->byLateAttend()->byStudentStatus($studentsDiabled)->count(); //For late students
                $forgot = $group->attendances()->byLectureId($lec->id)->byForgot()->byStudentStatus($studentsDiabled)->count(); //For forgot book

                $abscence = max($totalStudentsCount - $lectureAttendances, 0);
                // $attendOutsideGroup = Student::whereHas('attendances', function ($query) use ($lec, $group) {
                //     $query->where('lec_id', $lec->id);
                //     //     ->where('attend_group_id', '<>', 'students.group_id');
                // })
                //     ->byStage($group->stage_id)
                //     ->byEnabled()
                //     ->byGroup($group->id)
                //     ->pluck('code');

                // dd($attendOutsideGroup);

                $arr[] = $this->lecStstsArray($group->title, $lectureAttendances, $attends, $late, $forgot, $abscence, $totalStudentsCount, $studentsDiabled);

            }
        } else {
            $students = Student::byStage($lec->stage_id);
            $studentsDiabled = $students->byDisabled()->pluck('id');

            $totalStudentsCount = Student::byStage($lec->stage_id)->byEnabled()->count();

            $lectureAttendances = $lec->attendances()->byStudentStatus($studentsDiabled)->count();

            $attends = $lec->attendances()->byLectureId($lec->id)->byAttend()->byStudentStatus($studentsDiabled)->count(); //For attended students
            $late = $lec->attendances()->byLectureId($lec->id)->byLateAttend()->byStudentStatus($studentsDiabled)->count(); //For late students
            $forgot = $lec->attendances()->byLectureId($lec->id)->byForgot()->byStudentStatus($studentsDiabled)->count(); //For forgot book

            $abscence = $totalStudentsCount - $lectureAttendances;
            $arr[] = $this->lecStstsArray(null, $lectureAttendances, $attends, $late, $forgot, $abscence, $totalStudentsCount, $studentsDiabled);
        }
        $abscence = $totalStudentsCount - $lectureAttendances;

        return response()->json(
            [
                'stats' => $arr
            ]
        );
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
        $permissions = auth()->user()->getPermissions()['lectures'];
        if (isset($permissions) && isset($permissions['update']) && $permissions['update']) {
            $lecture = Lecture::find($request->lecture_id);
            if (isset($request->title))
                $lecture->title = $request->title;
            if (isset($request->lecture_date))
                $lecture->lecture_date = $request->lecture_date;
            $lecture->save();

            return response()->json(['lecture' => new LectureResource($lecture)]);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate(['lecture_id' => 'required|exists:lectures,id']);
        $permissions = auth()->user()->getPermissions()['lectures'];
        if (isset($permissions) && isset($permissions['delete']) && $permissions['delete']) {
            $lecture = Lecture::find($request->lecture_id);
            $lecture->delete();
            return response()->json(['message' => 'تم حذف المحاضرة بنجاح']);
        } else {
            return response()->json(['message' => 'ليس لديك صلاحية للقيام بهذا'], 401);
        }
    }
}