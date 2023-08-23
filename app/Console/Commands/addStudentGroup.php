<?php

namespace App\Console\Commands;

use App\Models\Group\Group;
use App\Models\Stages\Stage;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class addStudentGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:studentGroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stages = Stage::all();
        foreach ($stages as $stage) {
            $groups = Group::byStageId($stage->id)->get();
            foreach ($groups as $group) {
                $students = $stage->students()->get();
                foreach ($students as $student) {
                    if (Str::contains($student->name, '(' . $group->title . ')')) {
                        $student->group_id = $group->id;
                        $student->save();
                    }
                }

            }

        }
    }
}