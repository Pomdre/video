<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['auth:sanctum', 'verified'])->get('/', 'App\Http\Controllers\main@main', function () {
    return view('main');
})->name('main');

Route::middleware(['auth:sanctum', 'verified'])->get('/view', 'App\Http\Controllers\main@video', function () {
    return view('video');
});

Route::middleware(['auth:sanctum', 'verified'])->post('/vote', 'App\Http\Controllers\main@vote', function () {
});