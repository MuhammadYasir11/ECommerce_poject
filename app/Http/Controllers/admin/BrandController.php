<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands = Brands::latest();

        if (!empty($request->get('keyword'))) {
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
        }
        $brands =  $brands->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create(){
        return view('admin.brands.create');
    }

    public function store(Request $request){

        
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required'
        ]);

        if ($validator->passes()) {
                $brands = new Brands();
                $brands->name = $request->name;
                $brands->slug = $request->slug;
                $brands->status = $request->status;
                $brands->save();

                $request->session()->flash('success','Brands added successfully');

                return response()->json([
                    'status' => true,
                    'message' => 'Brands added successfully '
                ]);
    


        } else {
            return response()->json([
                'status' => false, 
                'errors' =>$validator->errors()
            ]);

        }
    }

    public function edit($id, request $request){

        $brand = Brands::find($id);

        if (empty($brand)){
            $request->session()->flash('error','Record Not Found');
            return redirect()->route('brands.index');
        }
        
            $data['brand'] = $brand;
            return view('admin.brands.edit',$data);
    }

    public function update($id, Request $request){

        $brand = Brands::find($id);

        if (empty($brand)){
            $request->session()->flash('error','Record Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'category Not Found' 
            ]);
            // return redirect()->route('brands.index');
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
            'status' => 'required',
            
        ]);

        if ($validator->passes()) {
                
                $brand->name = $request->name;
                $brand->slug = $request->slug;
                $brand->status = $request->status;
                $brand->save();

                $request->session()->flash('success','Brands Updated successfully');

                return response()->json([
                    'status' => true,
                    'message' => 'Brands Updated successfully '
                ]);
    


        } else {
            return response()->json([
                'status' => false, 
                'errors' =>$validator->errors()
            ]);

        }
}

    public function destroy($id,Request $request){

        $brand = Brands::find($id);

        if (empty($brand)) {
            $request->session()->flash('error', 'Record Not Found');
            return response([
                'status' => false,
                'notFound' => true,
                'message' => 'Brands Not Found' 
            ]);
        }
        $brand->delete();

        $request->session()->flash('success','Brands Deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category Brands successfully '
        ]);
    }
}
