<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FrontendController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CheckoutController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register',[AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('getCategory',[FrontendController::class,'category']);
Route::get('fetchproducts/{slug}',[FrontendController::class,'product']);
Route::get('view-product/{category_slug}/{product_slug}', [FrontendController::class,'viewproduct']);

Route::post('add-to-cart', [CartController::class,'addtocart']);
Route::get('cart', [CartController::class,'viewcart']);
Route::put('cart-updatequantity/{cart_id}/{scope}',[CartController::class,'updatequantity']);
Route::delete('delete-cartitem/{cart_id}',[CartController::class,'deleteCartItem']);

Route::post('validate-order',[CheckoutController::class,'validateOrder']);
Route::post('place-order',[CheckoutController::class,'placeorder']);
Route::post('charge',[CheckoutController::class,'charge']);

Route::middleware(['auth:sanctum','isAPIAdmin'])->group(function () {
    Route::get('/checkingAuthenticated', function() {
        return response()->json(['message' => 'You are in', 'status'=>200], 200);
    });

    // Category
    Route::apiResource('categories',CategoryController::class);
    Route::get('all-category',[CategoryController::class, 'allcategory']);

    //Product
    Route::apiResource('products',ProductController::class);

});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout',[AuthController::class, 'logout']);

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
