<?php

use App\Http\Middleware\AddResponseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController, StudentController,
    ExamController, GradeController,
    LectureController, AttendanceController,
    SearchController,HomeworkController
};
use App\Http\Controllers\PDF\PDFController;
use App\Http\Controllers\Stages\{StageController};

Route::middleware(AddResponseStatus::class)->group(function () {
    //Without token
    Route::post("register", [UserController::class, "register"]);
    Route::post("login", [UserController::class, "login"]);

    Route::get('/stages', [StageController::class, 'index']);

    //With tokens
    Route::group(["middleware" => ["auth:api"]], function () {
        Route::prefix('auth')->group(function () {
            Route::get("profile", [UserController::class, "profile"]);
            Route::get("attendance_stats", [UserController::class, "getAttendanceStats"]);
            Route::post('update', [UserController::class, 'update']);
            Route::post('changePhone', [UserController::class, 'changePhone']);
            Route::post('changePassword', [UserController::class, 'changePassword']);
        });
        Route::get("logout", [UserController::class, "logout"]);
        
        
        Route::prefix('students')->group(function () {
            Route::get('/', [StudentController::class, 'studentProfile']);
            Route::post('/create', [StudentController::class, 'store']);
            Route::post('/list', [StudentController::class, 'studentExamsInStage']);
            Route::post('/attendance', [StudentController::class, 'studentAttendancesInStage']);
            Route::post('/homeworks', [StudentController::class, 'studentHomeworksInStage']);
            Route::post('/profile', [StudentController::class, 'show']);
            Route::post('/update', [StudentController::class, 'update']);
            Route::post('/generate_pdf',[PDFController::class, 'generatePDF']);
        });
        Route::prefix('exams')->group(function () {
            Route::get('/', [ExamController::class, 'allExams']);
            Route::get('/stats', [ExamController::class, 'examStatistics']);
            Route::post('/collectiveExams', [ExamController::class, 'collectiveExams']);
            Route::post('/create', [ExamController::class, 'store']);
            Route::post('/update', [ExamController::class, 'update']);
            Route::post('/delete', [ExamController::class, 'destroy']);
        });

        Route::prefix('grades')->group(function () {
            Route::post('/store', [GradeController::class, 'store']);
        });

        Route::prefix('lectures')->group(function () {
            Route::post('/store', [LectureController::class, 'store']);
            Route::post('/update', [LectureController::class, 'update']);
            Route::post('/delete', [LectureController::class, 'destroy']);
            Route::get('/stats', [LectureController::class, 'lectureStats']);
        });

        Route::prefix('attendances')->group(function () {
            Route::post('/store', [AttendanceController::class, 'store']);
        });
        Route::prefix('homeworks')->group(function () {
            Route::post('/store', [HomeworkController::class, 'store']);
        });

        Route::prefix('search')->group(function () {
            Route::post('/',[SearchController::class, 'searchStudent']);
        });


    });
});