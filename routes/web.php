<?php

use App\Http\Controllers\SpaController;
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

Route::get('/{path}', SpaController::class)->where('path', '.*')->name('spa');
// don't delete route below; it's required for password reset; this route is handeled in spa route controller 
Route::get('/reset-password/{token}')->middleware('guest')->name('password.reset');
