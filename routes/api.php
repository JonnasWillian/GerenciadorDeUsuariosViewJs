<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Userarios;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/pegarUsuarios', [Userarios::class, 'view'])->name('usuarios');
Route::post('/usuarios', [Userarios::class, 'create'])->name('usuarios');
Route::put('/usuarios/{usuario}', [Userarios::class, 'update'])->name('usuarios');
Route::delete('/usuarios/{usuario}', [Userarios::class, 'destroy'])->name('usuarios');
