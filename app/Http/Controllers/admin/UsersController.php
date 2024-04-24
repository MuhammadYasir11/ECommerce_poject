<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\user;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(){
        $users = user::latest();
        

        $users =  $users->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create(){
        return view('admin.users.create');
    }
}
