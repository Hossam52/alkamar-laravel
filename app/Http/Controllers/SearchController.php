<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchStudent(Request $request)
    {
        $request->validate([
            'stage_id' => 'exists:stages,id',
            'code' => 'required_without:name|string',
            // 'name' => 'required_without:code |string',
        ]);
        $stage_id = $request->stage_id;
        $name = $request->name;
        $code = $request->code;

        $stds = Student::byStage($stage_id);
        if($name){
            $stds = $stds->where('name','like','%'. $name.'%');
        }
        elseif($code){
            $stds = $stds ->where('code',$code);
        }
        $stds = $stds->get()->sortBy('stage_id');

        return response()->json([
            'students'=> StudentResource::collection($stds),
        ]);

    }
}