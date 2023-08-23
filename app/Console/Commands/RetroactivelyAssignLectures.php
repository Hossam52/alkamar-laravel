<?php

namespace App\Console\Commands;

use App\Models\Group\Group;
use App\Models\Lecture;
use Illuminate\Console\Command;

class RetroactivelyAssignLectures extends Command
{
   
    protected $signature = 'lectures:retroassign';
    protected $description = 'Retroactively assigns lectures to groups';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $lectures = Lecture::all();

        foreach ($lectures as $lecture) {
            $stageId = $lecture->stage_id;
            $groups = Group::byStageId($stageId)->get();

            foreach ($groups as $group) {
                $existingLecture = $group->lectures()->where('lec_id', $lecture->id)->first();
                if(!$existingLecture)
                    $group->lectures()->attach([$lecture->id]);
            }
        }

        $this->info('Lectures retroactively assigned to groups.');
    }
}
