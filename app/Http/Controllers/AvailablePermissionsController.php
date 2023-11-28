<?php

namespace App\Http\Controllers;

use App\Models\AvailablePermissions;
use Illuminate\Http\Request;

class AvailablePermissionsController extends Controller
{
    public function index(){
        return response()->json(['available_permissions'=>AvailablePermissions::all()]);
    }
}
