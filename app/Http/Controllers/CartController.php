<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\Product;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomerAddress;
use App\Models\ShippingCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
   public function addToCart(Request $request) {

     $product = Product::with('product_images')->find($request->id);

     if ($product == null) {
          return response()->json([
               'status' => false,
               'message' => 'Product Not Found'
          ]);
     }

     if (Cart::count() > 0) {
          
          $cartContent = Cart::content();
          $productAlreadyExist = false;

          foreach ($cartContent as  $item) {
               if ($item->id == $product->id) {
                    $productAlreadyExist = true;
               }
          }

          if ($productAlreadyExist == false) {
               Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => 
               (!empty($product->product_images)) ? $product->product_images->first() : '']);

               $status = true;
               $message = '<strong>'.$product->title.'</strong> Added in your cart successfully';
               session()->flash('success', $message);

          } else {
               $status = false;
               $message = $product->title.'Already Product added in cart';
          }
     } else {

          echo "else part call";
          Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => 
          (!empty($product->product_images)) ? $product->product_images->first() : '']);
          $status = true;
          $message = '<strong>'.$product->title.'</strong> Added in your cart successfully';
          session()->flash('success', $message);
     }
     return response()->json([
          'status' => $status,
          'message' => $message
     ]);

   }

   public function Cart() {
     $cartContent = Cart::content();
     $data['cartContent'] = $cartContent;
     return view('front.cart',$data);
   }

   public function updateCart(Request $request) {
     $rowId = $request->rowId;
     $qty = $request->qty;
     $itemInfo = Cart::get($rowId);
     $product = Product::find($itemInfo->id);

     //check qty available in stock
     if ($product->track_qty == 'Yes') {
          if ($qty <= $product->qty) {
               Cart::update($rowId, $qty);
               $message = 'Cart Update successfully';
               $status = true;
               session()->flash('success' , $message);
          } else {
               $message = 'Selected qty('.$qty.') is not available in stock';
               $status = false;
               session()->flash('error' , $message);
          }
     } else {
          Cart::update($rowId, $qty);
          $message = 'Cart Update successfully';
          $status = true;
          session()->flash('success' , $message);
     }
     
     return response()->json([
          'status' => $status,
          'message' =>  $message

     ]);
   }

   public function deleteItem(Request $request) {

     $itemInfo = Cart::get($request->rowId);

     if ($itemInfo == null) {
          $errorMessage = 'Item not found in cart!';
          session()->flash('error',$errorMessage);
          return response()->json([
               'status' => false,
               'message' =>  $errorMessage
     
          ]);
     }
       Cart::remove($request->rowId);
      
       $message = 'Item remove from Cart successfully';
       session()->flash('success',$message);
          return response()->json([
               'status' => true,
               'message' =>  $message
     
          ]);
       

   }

   public function processCheckout(Request $request) {
     
     //step 1 Apply validation
     $validator = Validator::make($request->all(),[
          'first_name' => 'required|min:5',
          'last_name' => 'required|',
          'email' => 'required|email',
          'country' => 'required|',
          'address' => 'required|min:30',
          'city' => 'required',
          'state' => 'required',
          'zip' => 'required',
          'mobile' => 'required',
     ]);

     if ($validator->fails()) {
          return response()->json([
               'message' => 'Please fix the errors',
               'status' => false,
               'errors' => $validator->errors()
          ]);
     }

     //step 2 save user address

     $user = Auth::user();

     CustomerAddress::updateOrCreate(
          ['user_id' => $user->id],
          [
               'user_id' => $user->id,
               'first_name' => $request->first_name,
               'last_name' => $request->last_name,
               'email' => $request->email,
               'mobile' => $request->mobile,
               'country_id' => $request->country,
               'address' => $request->address,
               'apartment' => $request->appartment,
               'city' => $request->city,
               'state' => $request->state,
               'zip' => $request->zip,
          ]
          );

          // step 3 store data in orders table

          if ($request->payment_method == 'cod') {
               
               $discountCodeId = '';
               $promoCode = '';
               $shipping = 0;
               $discount = 0;
               $subTotal = Cart::subTotal(2,'.','');
               $grandTotal = $subTotal+$shipping;

               if (session()->has('code')) {
                    $code = session()->get('code');

                    if ($code->type == 'percent') {
                         $discount = ($code->discount_number/100)*$subTotal;
                    } else {
                         $discount = $code->discount_amount;
                    }

                    $discountCodeId = $code->id;
                    $promoCode = $code->code;
               }
               // Calculate Shipping Charge

               $shippingInfo = ShippingCharge::where('country_id',$request->country)->first();

               $totalQty = 0;
               foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
               }

               if ($shippingInfo != null) {
                    $shipping = ShippingCharge::where('country_id','rest_of_world')->first();
                    $shipping = $totalQty*$shippingInfo->amount;
                    $grandTotal = ($subTotal - $discount)+$shipping;
               }

               $order = new Order;
               $order->subtotal = $subTotal;
               $order->shipping = $shipping;
               $order->grand_total = $grandTotal;
               $order->dicount = $discount;
               $order->coupon_code_id = $discountCodeId;
               $order->coupon_code = $promoCode;
               $order->payment_status = 'not paid';
               $order->status = 'pending';
               $order->user_id = $user->id;
               $order->first_name = $request->first_name;
               $order->last_name = $request->last_name;
               $order->email = $request->email;
               $order->mobile = $request->mobile;
               $order->address = $request->address;
               $order->apartment = $request->appartment;
               $order->state = $request->state;
               $order->city = $request->city;
               $order->zip = $request->zip;
               $order->notes = $request->order_notes;
               $order->country_id = $request->country;
               $order->save();
          
              // step 4 store order items in order items table

          foreach (Cart::content() as  $item) {
               $orderItem = new OrderItem;
               $orderItem->product_id = $item->id;
               $orderItem->order_id = $order->id;
               $orderItem->name = $item->name;
               $orderItem->qty = $item->qty;
               $orderItem->price = $item->price;
               $orderItem->total = $item->price*$item->qty;
               $orderItem->save();
          }

          session()->flash('success','You have successfully placed your order.');
          Cart::destroy();

          return response()->json([
               'message' => 'Order saved successfully',
               'orderId' => $order->id,
               'status' => true
          ]);
     } else {

     }
     
   }

  public function checkout() {

     //if cart is empty redirect to cart page
     if (Cart::count() == 0) {
          return redirect()->route('front.cart');
     }
     if (Auth::check() == false) {

          if (!session()->has('url.intended')) {
               session(['url.intended' => url()->current()]);
          }
          
          return redirect()->route('account.login');
     }

     $customerAddress = CustomerAddress::where('user_id',Auth::user()->id)->first();
     
     session()->forget('url.intended');

     $countries = Country::orderBy('name','ASC')->get();

     return view('front.checkout',[
          'countries' => $countries,
          'customerAddress' => $customerAddress,
     ]);
  }

   public function thankyou($id) {
     return view('front.thanks',[
          'id' => $id
     ]);
   }

   public function getOrderSummery(Request $request) {
          if ($request->country_id > 0) {
               
               $subTotal = Cart::subtotal(2,'.','');

               $shippingInfo = ShippingCharge:: where('country_id',$request->country_id)->first();

               $totalQty = 0;
               foreach (Cart::content() as $item) {
                    $totalQty += $item->qty;
               }

               if ($shippingInfo != null) {
                    
                    $shippingCharge = $totalQty*$shippingInfo->amount;
                    $grandTotal = $subTotal+$shippingCharge;

                    return response()->json([
                         'status' => true,
                         'grandTotal' => $grandTotal,
                         'shippingCharge' => $shippingCharge,
                    ]);
               } else {
                    $shippingInfo = ShippingCharge:: where('country_id','rest_of_world')->first();

                    $shippingCharge = $totalQty*$shippingInfo->amount;
                    $grandTotal = $subTotal+$shippingCharge;

                    return response()->json([
                         'status' => true,
                         'grandTotal' => $grandTotal,
                         'shippingCharge' => $shippingCharge,
                    ]);
               }
          } else {

          }
   }
}
