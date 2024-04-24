<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index(){

        $products = Product::orderBy('id','DESC')->where('status','1')->get();
        $data['products'] = $products;
        return view('front.product',$data);
    }
}
