<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Grade;
use App\Models\Group\Group;
use App\Models\Lecture;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'stage_id'=>'required|exists:stages,id',
        ]);
        $groups = Group::byStageId($request->stage_id)->get();

        return response()->json([
            'groups'=>GroupResource::collection($groups)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'title'=>'required',
        ]);

        $arr = $request->all();
        $arr['created_by'] = $request->user()->id;
        $group = Group::create($arr);
        //should added to each lecture in the stage
        $lectures = Lecture::byStageId($request->stage_id)->get();
        foreach ($lectures as $lec) {
            $groups = [$group->id];
            $lec->storeGroup($groups);
        }

        return response()->json([
            'group'=>new GroupResource($group)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $groups)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $groups)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $groups)
    {
        //
    }
}