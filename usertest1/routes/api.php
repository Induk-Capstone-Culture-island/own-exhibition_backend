<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Public Routes
Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);
Route::get('/users/{id}',[UserController::class,'getUser']); # ex) user/1


#Rest API는 name규칙을 찾아보기

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/logout',[UserController::class,'logout']);
    Route::get('/userinfo',[UserController::class,'userinfo']);
    Route::post('/changepassword',[UserController::class,'changepassword']);
    Route::post('/delete',[UserController::class,'delete']);
});