<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\api\SurveyController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
  // logout route
  Route::post('logout', [AuthController::class, 'logout']);

  // dashboard route
  Route::get('dashboard', [DashboardController::class, 'index']);

  // surveys routes
  Route::apiResource('surveys', SurveyController::class);
});

// survey by slug route
Route::get('survey-by-slug/{survey:slug}', [SurveyController::class, 'showForGuest']);

// answer questions route
Route::post('surveys/{survey}/answer', [SurveyController::class, 'storeAnswer']);

// auth routes
Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);
