<?php

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
//                                          HOME ROUTE
Route::get('/', function () {
    return view('home');
})->name('home');

//                                     LOGIN AND REGISTER ROUTES
Route::post('/login',[\App\Http\Controllers\LogicController::class, "login"])->name("login");
Route::post('/register',[\App\Http\Controllers\LogicController::class, "register"])->name("register");


//                                      LOGGED IN ROUTES
Route::middleware(['loggedIn'])->group(function () {
    Route::get('/task', [\App\Http\Controllers\LogicController::class, "index"]);
    Route::post('/task/filter', [\App\Http\Controllers\LogicController::class, "index"])->name("filter");

    Route::post('/task', [\App\Http\Controllers\LogicController::class, "store"])->name("storeTask");
    Route::get('/task/{id}', [\App\Http\Controllers\LogicController::class, "edit"])->name("editTask");
    Route::put('/task/{id}', [\App\Http\Controllers\LogicController::class, "update"])->name("updateTask");
    Route::delete('/task/{id}', [\App\Http\Controllers\LogicController::class, "destroy"])->name("deleteTask");

    Route::get('/logout',[\App\Http\Controllers\LogicController::class, "logout"])->name("logout");
});
