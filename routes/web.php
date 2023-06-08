<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;

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
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'show']) -> name('login');

Route::post('/login', [LoginController::class, 'authenticate']) -> name('login.authenticate');

Route::post('/login/logout', [LoginController::class, 'logout']) -> name('login.logout') -> middleware('auth');

Route::get('/home', function() {
    return view('home');
}) -> name('home') -> middleware('auth');

Route::get('/users/create', [UserController::class, 'create']) -> name('users.create');

Route::post('/users', [UserController::class, 'store']) -> name('users.store');

Route::get('/users/{id}', [UserController::class, 'show']) -> name('users.show') -> middleware('auth');