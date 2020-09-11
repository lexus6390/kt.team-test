<?php

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

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

// Роуты сущности User
UserController::routesUser();

// Роуты сущности Task
TaskController::routesTask();
