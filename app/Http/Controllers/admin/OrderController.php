<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;

class OrderController extends Controller
{
    public function create()
    {
        return view('admin.orders.create');
    }

    public function index()
    {
        return view('admin.orders.index');
    }
}
