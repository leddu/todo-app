<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[\App\Http\Controllers\LogicController::class, "login"])->name("login");


Route::middleware(['loggedIn'])->group(function () {
    Route::get('/task', [\App\Http\Controllers\LogicController::class, "index"]);
    Route::post('/task/filter', [\App\Http\Controllers\LogicController::class, "index"])->name("filter");

    Route::post('/task', [\App\Http\Controllers\LogicController::class, "store"])->name("storeTask");
    Route::put('/task', [\App\Http\Controllers\LogicController::class, "update"])->name("updateTask");
    Route::delete('/task/{id}', [\App\Http\Controllers\LogicController::class, "destroy"])->name("deleteTask");

    Route::get('/logout',[\App\Http\Controllers\LogicController::class, "logout"])->name("logout");
});
