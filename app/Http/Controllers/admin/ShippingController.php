<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create() {
        $countries = Country::get();
        $data['countries'] = $countries;

        $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
                            ->leftJoin('countries','countries.id','shipping_charges.country_id')->get(); 
        $data['shippingCharges'] = $shippingCharges;

        return view('admin.shipping.create',$data);
    }

    public function store(Request $request) {        
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {

            $shipping = new ShippingCharge;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            session()->flash('success','Shipping Added Successfully');
             return response()->json([
                 'status' => true,
             ]);
            
        } else {
            return response()->json([
                'status' => false,
                'errors' =>$validator->errors()
            ]);
        }
    }

    public function edit($id) {

        $shippingCharge = ShippingCharge::find($id);

        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingCharge'] = $shippingCharge;
        
        return view('admin.shipping.edit',$data);
    }

    public function update($id, Request $request) {

        $shipping = ShippingCharge::find($id);
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {

            if ($shipping == null) {
          
                session()->flash('error','Shipping Not Found');
        
                return response()->json([
                    'status' => true,
                    
                ]);
            }
            
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping Update Successfully');

            return response()->json([
                'status' => true,
                
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id) {

        $shippingCharge = ShippingCharge::find($id);

        if ($shippingCharge == null) {
          
        session()->flash('error','Shipping Not Found');

        return response()->json([
            'status' => true,
            
        ]);
    }

        $shippingCharge->delete();

        session()->flash('success','Shipping Deleted Successfully');

        return response()->json([
            'status' => true,
            
        ]);
    }


}
