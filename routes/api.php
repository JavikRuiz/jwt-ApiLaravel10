<?php

use App\Http\Controllers\Api\CiudadController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);

Route::group([
    "middleware" => ["jwt.routes"]
], function(){
    Route::resource("users", UserController::class);
    Route::get("logout", [AuthController::class, "logout"]);
    Route::resource("tasks",TaskController::class);
    Route::get('filter-task/{status_name}', [TaskController::class, "status_task"]);
});