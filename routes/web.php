<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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


Route::middleware(['guest'])->group(function(){

Route::get('/sign-in', [AuthController::class,'showLoginForm'])->name('login');
Route::get('/sign-up', [AuthController::class,'showLoginForm'])->name('register');
});
Route::post('/do-sign-up', [AuthController::class,'register'])->name('register.dosignup');
Route::post('/do-sign-in', [AuthController::class,'login'])->name('login.dosignin');

Route::middleware(['auth'])->group(function(){
    Route::get('/', function () {
        return view('home');
    })->name('home');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');
Route::get('userlist', [MessageController::class,'user_list'])->name('user.list');
Route::get('message-readed/{id}', [MessageController::class,'message_readed'])->name('message.readed');
Route::get('usermessage/{id}/{page}/{limit}/{offsetId}', [MessageController::class,'user_message'])->name('user.message');
Route::post('senemessage', [MessageController::class,'send_message'])->name('user.message.send');
Route::get('deletesinglemessage/{id}', [MessageController::class,'delete_single_message'])->name('user.message.delete.single');
Route::get('deleteallmessage/{id}', [MessageController::class,'delete_all_message'])->name('user.message.delete.all');
Route::put('/profile-update', [HomeController::class,'profile_update'])->name('profile.update');
Route::get('/profile-edit', [HomeController::class,'profile_edit'])->name('profile.edit');


});

