<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('privatefiles')->with('success', 'Welcome Back');
    }

    return redirect("login");
});

/* Routes for pages */
Route::get('dashboard', [\App\Http\Controllers\CustomAuthController::class, 'dashboard'])->middleware('auth'); 
Route::get('login', [\App\Http\Controllers\CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [\App\Http\Controllers\CustomAuthController::class, 'customLogin'])->name('login.custom'); 
Route::get('registration', [\App\Http\Controllers\CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [\App\Http\Controllers\CustomAuthController::class, 'customRegistration'])->name('register.custom'); 
Route::get('signout', [\App\Http\Controllers\CustomAuthController::class, 'signOut'])->name('signout');

/* Route for private files */
Route::resource('/privatefiles', \App\Http\Controllers\PrivateFileController::class)->middleware('auth');
