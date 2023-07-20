<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVarificationMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::post('/userRegistration', [UserController::class, 'userRegistration']);
Route::post('/userLogin', [UserController::class, 'userLogin']);
Route::post('/sendOTPToEmail', [UserController::class, 'sendOTPToEmail']);
Route::post('/optverify', [UserController::class, 'optvarification']);

// Reset password and token varification with middleware
Route::post('/reset-password', [UserController::class, 'resetPassword'])
->middleware([TokenVarificationMiddleware::class]);



