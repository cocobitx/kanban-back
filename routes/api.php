<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/signin', [AuthController::class, 'signin']);

Route::group(['prefix' => 'articles', 'middleware' => 'verifiedjwt'], function () {
    Route::get('/',[ArticlesController::class, 'index']);
    Route::get('/{id}',[ArticlesController::class, 'show']);
    Route::post('/store',[ArticlesController::class, 'store']);
    Route::patch('/',[ArticlesController::class, 'update']);
    Route::delete('/',[ArticlesController::class, 'destroy']);
});