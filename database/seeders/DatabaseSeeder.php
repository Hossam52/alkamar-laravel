<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\StageType::create([
            'name' => 'المرحلة الاعدادية',
        ]);
        \App\Models\StageType::create([
            'name' => 'المرحلة الثانوية',
        ]);
        \App\Models\Stage::create([
            'stage_type_id' => 1,
            'title'=>'الصف الاول الاعدادي'
        ]);
        \App\Models\Stage::create([
            'stage_type_id' => 1,
            'title'=>'الصف الثاني الاعدادي'
        ]);
        \App\Models\Stage::create([
            'stage_type_id' => 1,
            'title'=>'الصف الثالث الاعدادي'
        ]);

        \App\Models\Stage::create([
            'stage_type_id' => 2,
            'title'=>'الصف الاول الثانوي'
        ]);
        \App\Models\Stage::create([
            'stage_type_id' => 2,
            'title'=>'الصف الثاني الثانوي'
        ]);
        \App\Models\Stage::create([
            'stage_type_id' => 2,
            'title'=>'الصف الثالث الثانوي'
        ]);


    }
}
