<?php

use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\RegistrasiPengurusController;
use Illuminate\Support\Facades\Route;

Route::get('/registrasi', [RegistrasiController::class, 'index']);
Route::get('/registrasi/getDesa/{id}', [RegistrasiController::class, 'getDesa']);
Route::get('/registrasi/getKelompok/{id}', [RegistrasiController::class, 'getKelompok']);
Route::post('/registrasi', [RegistrasiController::class, 'store']);

Route::get('/pengurus-daerah/registrasi-pengurus', [RegistrasiPengurusController::class, 'index']);
Route::get('/pengurus-daerah/registrasi-pengurus/getOptions/{tingkatan}', [RegistrasiPengurusController::class, 'getOptions']);
Route::post('/pengurus-daerah/registrasi-pengurus', [RegistrasiPengurusController::class, 'store']);

Route::get('/phpinfo', fn () => phpinfo());