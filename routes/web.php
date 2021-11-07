<?php

use App\Http\Controllers\Test\StampController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('stampauthenticateuser',[StampController::class,'getAuthenticateUser']);
Route::get('stampgetaccountinfo',[StampController::class, 'getAccountInfo']);
Route::get('purchasepostage',[StampController::class,'purchasePostage']);
// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
