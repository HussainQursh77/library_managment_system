<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;



Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

});


Route::apiResource('users', UserController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('ratings', RatingController::class);

Route::post('/borrow', [BorrowController::class, 'store']);
Route::post('/borrow/{id}/return', [BorrowController::class, 'returnBook']);
