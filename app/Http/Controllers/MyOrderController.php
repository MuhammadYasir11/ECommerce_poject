<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyOrderController extends Controller
{
    public function index(){
        return view('front.my-orders');
    }
}
