<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlmarhumController;

// Halaman utama
Route::get('/', function () {
    return view('index');
});

// Harus di ATAS resource
Route::get('/makam/{id}', [AlmarhumController::class, 'showPublic'])->name('almarhum.public.show');
Route::get('/almarhum/{id}/qr', [AlmarhumController::class, 'generateQr'])->name('almarhum.qr');

Route::resource('almarhum', AlmarhumController::class);