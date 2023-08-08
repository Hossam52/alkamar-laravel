<?php

namespace App\Models\Stages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StageType extends Model
{
    use HasFactory;
    public function stages(){
        return $this->hasMany(Stage::class);
    }

    protected $hidden =[
        'created_at',
        'updated_at',
    ];
}
