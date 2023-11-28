<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class available_permissions_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\AvailablePermissions::insert([
            ['module' => 'assistants', 'title' => 'المساعدين', 'view' => false, 'create' => false, 'update' => false, 'delete' => false],
            ['module' => 'attendances', 'title' => 'الحضور', 'view' => true, 'create' => true, 'update' => false, 'delete' => true],
            ['module' => 'exams', 'title' => 'الامتحانات', 'view' => true, 'create' => true, 'update' => true, 'delete' => true],
            ['module' => 'grades', 'title' => 'الدرجات', 'view' => true, 'create' => true, 'update' => true, 'delete' => false],
            ['module' => 'homeworks', 'title' => 'الواجبات', 'view' => true, 'create' => true, 'update' => true, 'delete' => false],
            ['module' => 'lectures', 'title' => 'المحاضرات', 'view' => true, 'create' => true, 'update' => true, 'delete' => true],
            ['module' => 'students', 'title' => 'الطلاب', 'view' => true, 'create' => true, 'update' => true, 'delete' => false],
            ['module' => 'student_payments', 'title' => 'مصاريف الطلاب', 'view' => true, 'create' => true, 'update' => true, 'delete' => false],
            ['module' => 'payments', 'title' => 'المصاريف', 'view' => true, 'create' => true, 'update' => false, 'delete' => false],
        ]);
    }
}
