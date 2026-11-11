<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'petugas'
            ? redirect()->route('petugas.dashboard')
            : redirect()->route('siswa.dashboard');
    }

    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Siswa routes (Only accessible by role: 'siswa')
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->as('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/katalog', [SiswaController::class, 'catalog'])->name('catalog');
    Route::post('/pinjam/{book}', [SiswaController::class, 'borrow'])->name('borrow');
    Route::post('/kembalikan/{loan}', [SiswaController::class, 'returnBook'])->name('return');
    Route::get('/tenggat-waktu', [SiswaController::class, 'deadlines'])->name('tenggat');
});

// Petugas routes (Only accessible by role: 'petugas')
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->as('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengawasan-pengembalian', [PetugasController::class, 'returns'])->name('returns');
    Route::get('/pengawasan-tenggat', [PetugasController::class, 'deadlines'])->name('deadlines');
    Route::get('/katalog-buku', [PetugasController::class, 'books'])->name('books');
    Route::get('/tambah-buku', [PetugasController::class, 'createBook'])->name('books.create');
    Route::post('/katalog-buku', [PetugasController::class, 'storeBook'])->name('books.store');
    Route::get('/buku/{book}/edit', [PetugasController::class, 'editBook'])->name('books.edit');
    Route::put('/buku/{book}', [PetugasController::class, 'updateBook'])->name('books.update');
    Route::delete('/buku/{book}', [PetugasController::class, 'destroyBook'])->name('books.destroy');
    Route::post('/konfirmasi-kembali/{loan}', [PetugasController::class, 'confirmReturn'])->name('confirmReturn');
});
