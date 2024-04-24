<?php
use App\Models\Category;
// use App\Models\Product;
// use App\Models\ProductImage;

 function getCategories(){
      return Category::orderBy('name','ASC')
      ->with('sub_category')
      ->orderBy('id','DESC')
      ->where('status',1)
      ->where('showHome','Yes')
      ->get();
 }

//  function getProducts(){
//     return Product::orderBy('title','ASC')
//     ->where('status',1)
//     ->get();
//  }

//  function getProductimage(){
//     return ProductImage::orderBy('image','ASC')->get();
    
//  }
?>