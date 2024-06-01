<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::prefix('/users')->controller(UserController::class)->group(function () {
        Route::get('/current', 'get');
        Route::delete('/logout', 'logout');
        Route::patch('/current', 'update');
    });

    Route::prefix('/contacts')->controller(ContactController::class)->group(function () {
        Route::post('', 'create');
        Route::get('/{id}', 'get')->where('id', '[0-9]+');
        Route::put('/{id}', 'update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'delete')->where('id', '[0-9]+');
    });
});