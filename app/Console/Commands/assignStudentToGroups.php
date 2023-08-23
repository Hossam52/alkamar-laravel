<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Group\Group;
use App\Models\Student;
use Illuminate\Console\Command;

class assignStudentToGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:studentGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'assign all student to first group in his assigned stage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $stds = Student::all();
        foreach ($stds as $student ) {
            $group = Group::byStageId($student->stage_id)->first();
            if($group){
                $student->group_id = $group->id;
                $student->save();
            }
        }
    }
}
