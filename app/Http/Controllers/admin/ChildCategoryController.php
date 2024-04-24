<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class ChildCategoryController extends Controller
{
    public function create() {

        $categories = Category::orderBy('name','ASC')->get();
        $subcategories = SubCategory::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['subcategories'] = $subcategories;
        return view('admin.child_category.create',$data);
    }
}
