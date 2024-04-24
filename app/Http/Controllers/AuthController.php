<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login() {

        return view('front.account.login');
    }

    public function register() {

        return view('front.account.register');
    }

    public function processRegister(Request $request) {

        $Validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if ($Validator->passes()) {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success', 'You have been register successfully');

            return response()->json([
                'status' => true,
            ]);
            
        } else {
            return response()->json([
                'status' => false,
                'errors' => $Validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request) {
        $Validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|'
        ]);

        if ($Validator->passes()) {

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get
            ('remember'))) {

                if (session()->has('url.intended')) {
                     return redirect(session()->get('url.intended'));
               }

                return redirect()->route('account.profile');

            } else {
                // session()->flash('error' , 'Either email/passowrd is incorrect');
                return redirect()->route('account.login')
                ->withInput($request->only('email'))
                ->with('error','Either email/passowrd is incorrect');
                
            }
           
        } else {
            return redirect()->route('account.login')
            ->withErrors($Validator)
            ->withInput($request->only('emaill'));
            
        }

    }

    public function profile() {
        return view('front.account.profile');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('account.login')
        ->with('success','You are successfully logout');
    }

    public function orders() {

       $user = Auth:: User();

       $orders = Order::where('user_id',$user->id)->orderBy('Created_at','DESC')->get();
       $data['orders'] = $orders;

        return view('front.account.order',$data);
    }
}
