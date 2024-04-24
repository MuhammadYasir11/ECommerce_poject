<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscountCoupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class DiscountCodeController extends Controller
{
    public function index() {

        return view('admin.coupon.index');
    }

    public function create() {

        return view('admin.coupon.create');
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);

        if ($validator->passes()) {

            //starting date must be greater then current date

            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($startAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' =>['starts_at' => 'Start date cannot be less then current date time']
                    ]);
                }
            }

            // expiry date must be greater then starting date

            if (!empty($request->starts_at) && !empty($request->expires_at) ) {
                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if ($expireAt->gt($startAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' =>['expires_at' => 'Expiry date cannot be greater then start date time']
                    ]);
                }
            }

            $discountCode = new DiscountCoupon;
            $discountCode->code = $request->code;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();

            session()->flash('success','Coupons Discount Added Successfully');
             return response()->json([
                 'status' => true,
                 'message' => 'Coupons Discount Added Successfully',
             ]);
            
        } else {
            return response()->json([
                'status' => false,
                'errors' =>$validator->errors()
            ]);
        }
    }

    public function edit() {
        
    }

    public function update() {
        
    }

    public function destroy() {
        
    }
}
