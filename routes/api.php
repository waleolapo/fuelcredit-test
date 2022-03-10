<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([ 'middleware' => [ 'json.response' ]], function() {

    Route::get('/', function() {
        return response()->json([
            'info' => 'Welcome to FuelCredit API, '
        ]);
    });
    Route::group([ 'prefix' => 'auth', ], function() {
        Route::post('/signup', [AuthController::class, 'register' ]);
        Route::post('/login', [AuthController::class, 'login' ]);
        Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout' ]);

    });
});

