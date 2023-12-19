<?php

use App\Http\Controllers\PrivateFileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SigCheckController;

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
/* Route for user profile */



Route::get('profile',[\App\Http\Controllers\ProfileController::class,'index'])->middleware('auth')->name('profile');
/* Routes for pages */
Route::get('dashboard', [\App\Http\Controllers\CustomAuthController::class, 'dashboard'])->middleware('auth')->name('dashboard'); 
Route::get('login', [\App\Http\Controllers\CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [\App\Http\Controllers\CustomAuthController::class, 'customLogin'])->name('login.custom'); 
Route::get('registration', [\App\Http\Controllers\CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [\App\Http\Controllers\CustomAuthController::class, 'customRegistration'])->name('register.custom'); 
Route::get('signout', [\App\Http\Controllers\CustomAuthController::class, 'signOut'])->name('signout');

/* Route for private files */
Route::resource('/privatefiles', \App\Http\Controllers\PrivateFileController::class)->middleware('auth');
Route::get("/download/{path}", '\App\Http\Controllers\PrivateFileController@download')->middleware('auth');
Route::resource("/share", ShareController::class)->middleware('auth');

Route::get('/download-index', [ShareController::class, 'download_index'])->middleware('auth')->name('download-index');
Route::get('/download-key', [ShareController::class, 'downloadKey'])->middleware('auth')->name('download-key');
Route::post('/download-shared', [ShareController::class, 'download_shared'])->middleware('auth')->name('download-shared');

Route::post('/pdf-check-signature', [SigCheckController::class, 'pdfSigCheck'])->middleware('auth')->name('pdf-check-signature');
Route::get('/pdf-check-signature-index', [SigCheckController::class, 'index'])->middleware('auth')->name('pdf-check-signature-index');