<?php

use App\Http\Controllers\RegistrasiController;
use Illuminate\Support\Facades\Route;

Route::get('/registrasi', [RegistrasiController::class, 'index']);
Route::get('/registrasi/getDesa/{id}', [RegistrasiController::class, 'getDesa']);
Route::get('/registrasi/getKelompok/{id}', [RegistrasiController::class, 'getKelompok']);
Route::post('/registrasi', [RegistrasiController::class, 'store']);
