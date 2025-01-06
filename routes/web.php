<?php

use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\RegistrasiPengurusController;
use App\Livewire\FormKegiatan;
use App\Livewire\FormPerizinanKegiatan;
use App\Livewire\SearchPesertaKegiatan;
use Illuminate\Support\Facades\Route;

Route::get('/registrasi', [RegistrasiController::class, 'index'])->name('form-registrasi');
Route::get('/registrasi/getDesa/{id}', [RegistrasiController::class, 'getDesa']);
Route::get('/registrasi/getKelompok/{id}', [RegistrasiController::class, 'getKelompok']);
Route::post('/registrasi', [RegistrasiController::class, 'store']);

Route::get('/pengurus-daerah/registrasi-pengurus', [RegistrasiPengurusController::class, 'index']);
Route::get('/pengurus-daerah/registrasi-pengurus/getOptions/{tingkatan}', [RegistrasiPengurusController::class, 'getOptions']);
Route::post('/pengurus-daerah/registrasi-pengurus', [RegistrasiPengurusController::class, 'store']);

Route::get('/presensi-mudamudi/{kegiatan}', FormKegiatan::class);
Route::get('/presensi-mudamudi/{kegiatan}/hadir', SearchPesertaKegiatan::class);
Route::get('/presensi-mudamudi/{kegiatan}/izin', FormPerizinanKegiatan::class);