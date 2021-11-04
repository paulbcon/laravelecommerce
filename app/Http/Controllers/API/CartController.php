<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addtocart(Request $request)
    {
        if (auth('sanctum')->check())
        {
            $user_id = auth('sanctum')->user()->id;
            $product_id = $request->product_id;
            $product_quantity = $request->product_quantity;

            $productCheck = Product::whereId($product_id)->first();

            if ($productCheck)
            {

                    if (CartProduct::where('product_id', $product_id)->where('user_id',$user_id)->exists())
                    {
                        return response()->json([
                            'status' => 409,
                            'message' => $productCheck->name . " already Added to Cart",
                        ]);
                    } else {
                        $cartdata = new Cart();
                        $cartdata->user_id = $user_id;
                        $cartdata->save();
                        $latestcart = Cart::latest('id')->first();
                        $cartitem = new CartProduct();
                        $cartitem->cart_id = $latestcart->id;
                        $cartitem->user_id = $user_id;
                        $cartitem->product_id = $product_id;
                        $cartitem->quantity = $product_quantity;
                        $cartitem->save();
                        return response()->json([
                            'status' => 201,
                            'message' => "Added to Cart",
                        ]);
                    }

            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "Product Not Found",
                ]);
            }


        } else {
            return response()->json([
                'status' => 401,
                'message' => "Login to Add to Cart",
            ]);
        }
    }

    public function viewcart()
    {
        if (auth('sanctum')->check())
        {
            $user_id = auth('sanctum')->user()->id;
            $cartitems = CartProduct::where('user_id',$user_id)->get();
            return response()->json([
                'status' => 200,
                'cart' => $cartitems,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Login to View Cart Data",
            ]);
        }
    }

    public function updatequantity($cart_id, $scope)
    {
        if (auth('sanctum')->check())
        {
             $user_id = auth('sanctum')->user()->id;
             $cartitem = CartProduct::where('cart_id',$cart_id)->where('user_id', $user_id)->first();

             if ($scope == "inc"){
                 $cartitem->quantity += 1;
             } else {
                 $cartitem->quantity -= 1;
             }

             $cartitem->update();

            return response()->json([
                'status' => 200,
                'message' => "Quantity Updated",
            ]);

        }
        else {
            return response()->json([
                'status' => 401,
                'message' => "Login to continue",
            ]);
        }
    }
}
