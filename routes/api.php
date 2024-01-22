<?php

use Illuminate\Support\Facades\Route;
use NotFound\Framework\Auth\Middleware\EnsureEmailIsVerified;
use NotFound\ListBoss\Http\Controllers\ListBossController;

// ContentBlock
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
Route::prefix(config('siteboss.api_prefix'))->group(function () {
    // Authenticated routes
    Route::group(['middleware' => ['auth:openid', 'api', EnsureEmailIsVerified::class]], function () {
        // Language for messages (not the language used for storing data)
        Route::group(['prefix' => '/{locale}', 'middleware' => 'set-forget-locale'], function () {
            Route::get('app/listboss', [ListBossController::class, 'index']);
        });
    });
});
