<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailablePermissions extends Model
{
    use HasFactory;

    protected $casts =[
        'view' => 'boolean',
        'update' => 'boolean',
        'create' => 'boolean',
        'delete' => 'boolean',
    ];
}
