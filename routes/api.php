<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Userarios;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/usuarios', [Userarios::class, 'view'])->name('usuarios');
Route::post('/usuarios', [Userarios::class, 'create'])->name('usuarios');
