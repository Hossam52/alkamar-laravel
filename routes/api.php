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
use App\Http\Controllers\Payments\{PaymentsController,StudentPaymentsController};
use App\Http\Controllers\Stages\{StageController};
use App\Http\Controllers\Group\{GroupsController};
use App\Http\Middleware\PaginateStudentList;

// Route::prefix('payments')->group(function () {
//     Route::post('/store', [PaymentsController::class, 'store']);
//     Route::get('/stats', [PaymentsController::class, 'payment_stats']);
//     Route::post('/store_student_payment', [StudentPaymentsController::class, 'store']);
// });
    //Without token

    // Route::prefix('students')->group(function () {
    //     Route::get('/', [StudentController::class, 'studentProfile']);
    //     Route::post('/generate_pdf',[PDFController::class, 'generatePDF']);
        
    //     Route::post('/create', [StudentController::class, 'store']);
    //     Route::post('/profile', [StudentController::class, 'show']);
    //     Route::post('/update', [StudentController::class, 'update']);
        
    //     Route:: post('/homeworks', [StudentController::class, 'studentHomeworksInStage']);
    //     Route::middleware(['responseStatus:with_total_students'])->post('/attendance', [StudentController::class, 'studentAttendancesInStage']);
    //     Route::post('/list', [StudentController::class, 'studentExamsInStage']);
    //     Route::post('/payments', [StudentPaymentsController::class, 'index']);

    // });
Route::middleware('responseStatus')->group(function(){
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
            Route::post('/generate_pdf',[PDFController::class, 'generatePDF']);
            
            Route::post('/create', [StudentController::class, 'store']);
            Route::post('/profile', [StudentController::class, 'show']);
            Route::post('/update', [StudentController::class, 'update']);
            
            Route::withoutMiddleware('responseStatus')->middleware(["responseStatus:with_total_students"])->group( function () {
                Route:: post('/homeworks', [StudentController::class, 'studentHomeworksInStage']);
                Route::post('/attendance', [StudentController::class, 'studentAttendancesInStage']);
                Route::post('/list', [StudentController::class, 'studentExamsInStage']);
                Route::post('/payments', [StudentPaymentsController::class, 'index']);

            });
        });
        Route::prefix('exams')->group(function () {
            Route::get('/', [ExamController::class, 'allExams']);
            Route::get('/stats', [ExamController::class, 'examStatistics']);
            Route::post('/collectiveExams', [ExamController::class, 'collectiveExams']);
            Route::post('/create', [ExamController::class, 'store']);
            Route::post('/update', [ExamController::class, 'update']);
            Route::post('/delete', [ExamController::class, 'destroy']);
        });

        Route::prefix('payments')->group(function () {
            Route::post('/store', [PaymentsController::class, 'store']);
            Route::get('/stats', [PaymentsController::class, 'payment_stats']);
            Route::post('/store_student_payment', [StudentPaymentsController::class, 'store']);
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

        Route::prefix('groups')->group(function () {
            Route::get('/',[GroupsController::class, 'index']);
            Route::post('/store',[GroupsController::class, 'store']);
        });


    });
});
