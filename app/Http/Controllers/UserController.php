<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Attendance;
use App\Models\Lecture;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{ // USER REGISTER API - POST
    public function register(Request $request)
    {
        // validation
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            'phone' => 'required|max:15|min:10|unique:users',
            "password" => "required|confirmed"
        ]);
        // create user data + save
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->save();

        $token = auth()->attempt(["email" => $request->email, "password" => $request->password]);
        $request->headers->add(['Authroization' => 'Bearer ' . $token]);
        // send response
        return response()->json([
            "message" => "تم تسجيل حساب بنجاح",
            "access_token" => $token,
            'user' => new UserResource(auth()->user()),
        ], 200);
    }
    // USER LOGIN API - POST
    public function login(Request $request)
    {
        // validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        // verify user + token
        if (!$token = auth()->attempt(["email" => $request->email, "password" => $request->password])) {

            return response()->json([
                "message" => "خطأ في البيانات"
            ],400);
        }
        
        return response()->json([
            "message" => "تم تسجيل الدخول بنجاح",
            "access_token" => $token,
            'user' =>new UserResource(auth()->user()),
        ]);
    }
    // USER PROFILE API - GET
    public function profile()
    {
        $user_data = auth()->user();
        return response()->json([
            "message" => "بيانات المستخدم",
            "user" =>  new UserResource( $user_data)
        ]);
    }
    public function getAttendanceStats(Request $request){
        $request->validate([
            'lec_id'=>'required|integer|exists:lectures,id'
        ]);

        $studentIdsByAssistant = Attendance::byStudentsScanned($request->lec_id,$request->user()->id)->get(['student_id']);
        $maleStds = Student::byMaleCount($studentIdsByAssistant)->count();
        $femaleStds = Student::byFemaleCount($studentIdsByAssistant)->count();

        return response()->json([
            'male'=>$maleStds,
            'female'=>$femaleStds
        ]);
    }
    // USER LOGOUT API - GET
    public function logout()
    {
        auth()->logout();
        return response()->json([
            "message" => "تم تسجيل الخروج بنجاح"
        ]);
    }
    public function changePhone(Request $request)
    {
        $user = auth()->user();
        $oldPassword = $user->password;
        $hashCheck = Hash::check($request->password, $oldPassword);
        $request->request->add(['password check' => $hashCheck]);

        $validator = $request->validate([
            'phone' => 'required|unique:users',
            'password' => 'required',
            'password check' => 'accepted'
        ]);
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'message' => 'تم تحديث الهاتف بنجاح'
        ]);

    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');

        $rules = array(
            'password' => 'required'
        );
        if(isset($email)){
            $rules['email']='email|unique:users,email,' . $user->id;

        }
        if(isset($phone)){
            $rules['phone']='unique:users,phone,' . $user->id;
        }

        $request->validate($rules);
        if (!Hash::check($request->input('password'), $user->getAuthPassword())) {
            return response()->json([
                'message' => 'خطأ في كلمة المرور'
            ],400);
        }

       
        if (!isset($name) && !isset($email) && !isset($phone)) {
            return response()->json([
                'message' => 'يجب ادخال البيانات'
            ],400);
        }
        if (isset($name))
            $user->name = $name;
        if (isset($email))
            $user->email = $email;
        if (isset($phone))
            $user->phone = $phone;
        $user->save();
        return response()->json([
            'message' => 'تم التحديث بنجاح',
            'user' => $user
        ]);

    }

    public function changePassword(Request $request)
    {


        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);
        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'خطأ في كلمة المرور'
            ],400);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json([
            'message' => 'تم تحديث كلمة المرور بنجاح'
        ]);
    }

}