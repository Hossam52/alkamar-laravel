<?php

use App\Http\Middleware\AddResponseStatus;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PDF\PDFController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',function(){
    return view('pdf.student_qr_codes');
});
