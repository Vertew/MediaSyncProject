<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageEvent;

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

Route::get('/test', function() {
    event(new MessageEvent());
}) -> name('test');

Route::get('/ws', function(){
    return view('websocket');
});

Route::post('/input-message', function(Request $request){
    event(new MessageEvent($request->message, auth()->user()));
    return null;
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

Route::get('/videos/show/{id?}', [ VideoController::class, 'show' ])->name('videos.show');

Route::get('/rooms/create', [RoomController::class, 'create']) -> name('rooms.create');

Route::post('/rooms', [RoomController::class, 'store']) -> name('rooms.store');

Route::get('/rooms/{key}', [ RoomController::class, 'show' ])->name('rooms.show');

Route::delete('/rooms/{id}', [RoomController::class, 'destroy']) -> name('rooms.destroy');

Route::post('/files/upload', [ FileController::class, 'store' ])->name('files.store');
