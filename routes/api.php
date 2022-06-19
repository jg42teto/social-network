<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HashtagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicStorageController;
use App\Http\Controllers\UserController;
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

Route::prefix('auth')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('forgot-password', [ResetPasswordController::class, 'send']);
    Route::post('reset-password', [ResetPasswordController::class, 'reset']);
});
Route::prefix('posts')->middleware('auth:sanctum')->group(function () {
    Route::get('', [PostController::class, 'home']);
    Route::post('', [PostController::class, 'store']);
    Route::delete('{id}', [PostController::class, 'destroy'])->whereNumber('id');
    Route::patch('{id}/toggle-like', [PostController::class, 'toggleLike'])->whereNumber('id');
    Route::patch('{id}/toggle-repost', [PostController::class, 'toggleRepost'])->whereNumber('id');
    Route::get('{id}/ancestry', [PostController::class, 'ancestry'])->whereNumber('id');
    Route::get('{id}/replies', [PostController::class, 'replies'])->whereNumber('id');
    Route::get('info', [PostController::class, 'userSpecificInfo']);
    Route::get('mentions', [PostController::class, 'mentions']);
});
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('', [UserController::class, 'index']);
    Route::get('{id}/posts/wall', [UserController::class, 'wallPosts'])->whereNumber('id');;
    Route::get('{id}/posts/all', [UserController::class, 'allPosts'])->whereNumber('id');;
    Route::get('{id}/posts/liked', [UserController::class, 'likedPosts'])->whereNumber('id');;
    Route::get('search', [UserController::class, 'search']);
    Route::put('', [UserController::class, 'update']);
    Route::patch('{id}/toggle-follow', [UserController::class, 'toggleFollow']);
    Route::get('data', [UserController::class, 'userData']);
    Route::patch('notifications-checked', [UserController::class, 'notificationsChecked']);
});
Route::post('storage', [PublicStorageController::class, 'store'])->middleware('auth:sanctum');
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('admins', [AdminController::class, 'viewAdmins']);
    Route::patch('admins/{id}/toggle-admin', [AdminController::class, 'toggleAdmin'])->middleware('admin.super');
    Route::get('users/{username}', [AdminController::class, 'viewUser']);
    Route::patch('users/{id}/toggle-block', [AdminController::class, 'toggleBlock']);
});
Route::prefix('hashtag')->middleware('auth:sanctum')->group(function () {
    Route::get('{keyword}/posts', [HashtagController::class, 'posts']);
    Route::get('search', [HashtagController::class, 'search']);
});
