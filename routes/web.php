<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Simple home route - no redirects
Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirect - only for authenticated users
Route::get('/dashboard', function () {
    if(auth()->user()->usertype == 'admin'){
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/books', [AdminController::class, 'allbooks'])->name('allbooks');
    Route::get('/books/add', [AdminController::class, 'addbook'])->name('addbook');
    Route::post('/books/create', [AdminController::class, 'createbook'])->name('createbook');
    Route::get('/books/edit/{id}', [AdminController::class, 'editbook'])->name('editbook');
    Route::put('/books/update/{id}', [AdminController::class, 'updatebook'])->name('updatebook');
    Route::delete('/books/delete/{id}', [AdminController::class, 'deletebook'])->name('deletebook');
    Route::get('/users', [AdminController::class, 'allusers'])->name('allusers');
    Route::get('/users/search', [AdminController::class, 'searchuser'])->name('searchuser');
    Route::get('/users/{id}', [AdminController::class, 'userdetails'])->name('userdetails');
    Route::get('/borrowed-books', [AdminController::class, 'borrowedbooks'])->name('borrowedbooks');
    Route::post('/return-book/{id}', [AdminController::class, 'returnbook'])->name('returnbook');
    Route::get('/borrow-history', [AdminController::class, 'borrowhistory'])->name('borrowhistory');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [AdminController::class, 'updateprofile'])->name('updateprofile');
});

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/books', [StudentController::class, 'allbooks'])->name('allbooks');
    Route::get('/books/{id}', [StudentController::class, 'bookdetails'])->name('bookdetails');
    Route::post('/borrow-book', [StudentController::class, 'borrowbook'])->name('borrowbook');
    Route::post('/return-book/{id}', [StudentController::class, 'returnbook'])->name('returnbook');
    Route::get('/borrow-history', [StudentController::class, 'borrowhistory'])->name('borrowhistory');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [StudentController::class, 'updateprofile'])->name('updateprofile');
});

// Auth Routes (from Breeze)
require __DIR__.'/auth.php';