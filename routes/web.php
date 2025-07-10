<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrotikConnectionController;
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

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);

// Hotspot
Route::get('/user', [App\Http\Controllers\UserController::class, 'index']);
Route::get('/hotspot/user/add', [App\Http\Controllers\UserController::class, 'add']);
Route::post('/hotspot/user/store', [App\Http\Controllers\UserController::class, 'store']);
Route::post('/hotspot/user/quick', [App\Http\Controllers\UserController::class, 'quick']);
Route::get('/hotspot/user/remove/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
Route::post('/hotspot/user/delete-all', [App\Http\Controllers\UserController::class, 'deleteAll']);

// Webview Untuk Menampilkan Fitur dari Mikrotik
Route::get('/user-list', [App\Http\Controllers\HomeController::class, 'userList'])->name('user.list');
Route::get('/user-list/export', [App\Http\Controllers\HomeController::class, 'exportUserCsv'])->name('user.export');
Route::get('/queue-list', [App\Http\Controllers\HomeController::class, 'queueList'])->name('queue.list');
Route::get('/api/traffic', [App\Http\Controllers\HomeController::class, 'apiTraffic']);

// API untuk status queue (sidebar)
Route::get('/api/queue-status', [App\Http\Controllers\HomeController::class, 'apiQueueStatus']);

// Tools
Route::get('/tools/ping', [App\Http\Controllers\ToolsController::class, 'pingForm']);
Route::post('/tools/ping', [App\Http\Controllers\ToolsController::class, 'ping']);
Route::get('/tools/access-link', [App\Http\Controllers\ToolsController::class, 'accessLink']);

// Auth
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

// Mikrotik Connection
Route::get('/connections', [MikrotikConnectionController::class, 'index'])->name('connections.index');
Route::post('/connections/login', [MikrotikConnectionController::class, 'login'])->name('connections.login');
Route::delete('/connection/{id}', [MikrotikConnectionController::class, 'destroy'])->name('connections.destroy');