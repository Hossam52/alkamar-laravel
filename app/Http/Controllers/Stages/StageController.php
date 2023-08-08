<?php

namespace App\Http\Controllers\Stages;

use App\Http\Resources\Stages\AllStageTypeResource;
use App\Models\Stages\Stage;
use App\Models\Stages\StageType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stageTypes = StageType::all();
        return response()->json([
            'stage_types'=> AllStageTypeResource::collection($stageTypes),
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Stage $stage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stage $stage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stage $stage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stage $stage)
    {
        //
    }
}
