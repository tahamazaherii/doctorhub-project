<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get("/" , [HomeController::class , 'index']);

Route::post('/register' , [AuthController::class , 'register']);
Route::post('/login' , [AuthController::class , 'login']);



Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::apiResource('posts', PostController::class); // apiResource post
    Route::post('/logout', [AuthController::class, 'logout']);
});
