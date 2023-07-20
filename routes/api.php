<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestApiController;
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
Route::get('/cau1', [TestApiController::class, 'cau1']);
Route::get('/cau2', [TestApiController::class, 'cau2']);
Route::delete('/cau3', [TestApiController::class, 'cau3']);
Route::get('/cau4', [TestApiController::class, 'cau4']);
Route::get('/cau5', [TestApiController::class, 'cau5']);
Route::get('/cau6', [TestApiController::class, 'cau6']);
Route::get('/cau7', [TestApiController::class, 'cau7']);
Route::get('/cau8', [TestApiController::class, 'cau8']);
Route::get('/cau9', [TestApiController::class, 'cau9']);
Route::get('/cau10', [TestApiController::class, 'cau10']);


