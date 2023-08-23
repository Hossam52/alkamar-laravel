<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Group\Group;
use Illuminate\Console\Command;

class assignAttendanceToGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:attendanceToGroup';

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
        $attends = Attendance::all();
        foreach ($attends as $attend ) {
            $student = $attend->student()->first();
            
            $group = Group::find($student->group_id);
            if($group){
                $attend->attend_group_id = $group->id;
                $attend->save();
            }
        }
        
    }
}
