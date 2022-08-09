<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
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

# Auth
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::get('users/{user}/profile-photo', [UsersController::class, 'profilePhoto']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    #Profile
    Route::get('me', [UsersController::class, 'getOwnProfile']);
    Route::post('me', [UsersController::class, 'updateOwnProfile']);
    Route::post('me/photo', [UsersController::class, 'updateProfilePhoto']);
    Route::post('change-password', [UsersController::class, 'changePassword']);

    #Directories
    Route::group(['prefix' => 'directories'], function () {

    });

    #Users
    Route::group(['prefix' => 'users'], function () {
        Route::get('', [UsersController::class, 'index']);
        Route::get('{user}', [UsersController::class, 'show']);
        Route::post('{user}', [UsersController::class, 'update']);
        Route::post('{user}/photo', [UsersController::class, 'updateProfilePhoto']);
    });

    #Settings
    Route::group(['prefix' => 'settings'], function () {
        Route::get('', [SettingController::class, 'index']);
        Route::get('{setting}', [SettingController::class, 'show']);

        Route::post('', [SettingController::class, 'store']);
        Route::post('{setting}', [SettingController::class, 'update']);
        Route::delete('{setting}', [SettingController::class, 'destroy']);
    });

    Route::group(['prefix' => 'pages'], function () {
        Route::get('', [PageController::class, 'index']);
        Route::get('{page}', [PageController::class, 'show']);

        Route::post('', [PageController::class, 'store']);
        Route::post('{page}', [PageController::class, 'update']);
        Route::delete('{page}', [PageController::class, 'destroy']);
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('', [CategoryController::class, 'index']);

        Route::post('', [CategoryController::class, 'store']);
        Route::post('{category}', [CategoryController::class, 'update']);
        Route::delete('{category}', [CategoryController::class, 'destroy']);
    });

    Route::group(['prefix' => 'menu'], function () {
        Route::get('', [MenuController::class, 'index']);

        Route::post('', [MenuController::class, 'store']);
        Route::post('{menu}', [MenuController::class, 'update']);
        Route::delete('{menu}', [MenuController::class, 'destroy']);
    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('', [PostController::class, 'index']);
//        Route::get('{post}', [PostController::class, 'show']);

        Route::post('', [PostController::class, 'store']);
        Route::post('{post}', [PostController::class, 'update']);
        Route::delete('{post}', [PostController::class, 'destroy']);
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
