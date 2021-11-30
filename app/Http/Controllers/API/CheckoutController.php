<?php

namespace App\Http\Controllers\API;

use Stripe\Stripe;
use App\Models\Cart;
use App\Models\Order;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function placeorder(Request $request)
    {
        if (auth('sanctum')->check())
        {
            $validator = Validator::make($request->all(), [
                'firstname'=> 'required|max:191',
                'lastname' => 'required|max:191',
                'phone' => 'required:max:12',
                'email' => 'required|email',
                'address'=> 'required',
                'city' => 'required',
                'state' => 'required|min:2|max:191',
                'zipcode'=>'required'
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->errors(),
                ]);
            } else
             {
                $userid = auth('sanctum')->user()->id;
                $order = new Order();
                $order->user_id = $userid;
                $order->firstname = $request->firstname;
                $order->lastname = $request->lastname;
                $order->phone = $request->phone;
                $order->email = $request->email;
                $order->address = $request->address;
                $order->city = $request->city;
                $order->state = $request->state;
                $order->zipcode = $request->zipcode;

                $order->payment_mode = $request->payment_mode;
                $order->tracking_no = "ARLENEDUBAIGOLD".rand(1111111,999999);
                $order->save();

                $carts = CartProduct::where('user_id',$userid)->get();

                $orderitems = [];
                foreach($carts as $item)
                {
                    $orderitems[] = [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->selling_price
                    ];

                    $item->product->update([
                        'quantity' => $item->product->quantity - $item->quantity
                    ]);


                    $order->orderproducts()->createMany($orderitems);

                    CartProduct::where('user_id',$userid)->delete();
                    Cart::where('user_id',$userid)->delete();

                    return response()->json([
                        'status' => 200,
                        'message' => 'Order Placed Successfully',
                    ]);

                }

            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to Continue'
            ]);
        }
    }

    public function validateOrder(Request $request)
    {
        if (auth('sanctum')->check())
        {
            $validator = Validator::make($request->all(), [
                'firstname'=> 'required|max:191',
                'lastname' => 'required|max:191',
                'phone' => 'required:max:12',
                'email' => 'required|email',
                'address'=> 'required',
                'city' => 'required',
                'state' => 'required|min:2|max:191',
                'zipcode'=>'required'
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->errors(),
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'Form Validated Successfully',
                ]);
            }
        }
        else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to Continue'
            ]);
        }
    }

    public function charge(Request $request)
    {
        //Set your secret key
        Stripe::setApiKey(env("STRIPE_SECRET"));

        $intent = PaymentIntent::create(
            [
                'amount' => $request->amount * 100,
                'currency' => "usd",
                'automatic_payment_methods' => [
                    'enabled' => true
                ],
            ]
            );

        $client_secret = $intent->client_secret;

        //Pass the client secret to the client
        return response()->json([
            'status' => 200,
            'clientSecret' => $client_secret
        ]);

    }
}
