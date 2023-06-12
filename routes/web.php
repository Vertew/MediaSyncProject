<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;

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

Route::get('/home', function() {
    return view('home');
}) -> name('home');

Route::get('/login', [LoginController::class, 'show']) -> name('login');

Route::post('/login', [LoginController::class, 'authenticate']) -> name('login.authenticate');

Route::post('/login/logout', [LoginController::class, 'logout']) -> name('login.logout') -> middleware('auth');

Route::get('/users/create', [UserController::class, 'create']) -> name('users.create');

Route::post('/users', [UserController::class, 'store']) -> name('users.store');

Route::get('/users/{id}', [UserController::class, 'show']) -> name('users.show') -> middleware('auth');

Route::get('/profiles/{id}', [ProfileController::class, 'show']) -> name('profiles.show') -> middleware('auth');

Route::get('/profiles/edit/{id}', [ProfileController::class, 'edit']) -> name('profiles.edit') -> middleware('auth');

Route::post('/profiles/update/{id}', [ProfileController::class, 'update']) -> name('profiles.update') -> middleware('auth');

Route::get('/videos/upload', [ VideoController::class, 'create' ])->name('videos.create');

Route::post('/videos/upload', [ VideoController::class, 'store' ])->name('videos.store');

Route::get('/videos/index', [ VideoController::class, 'index_user' ])->name('videos.index_user');

Route::get('/videos/room', [ VideoController::class, 'room' ])->name('videos.room');

Route::get('/videos/show/{id?}', [ VideoController::class, 'show' ])->name('videos.show');
