<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Route::resource('users', UserController::class)->middleware('auth');

Route::get("/users", [UserController::class, 'index'])->name("users.index")
    ->middleware("auth");

Route::get("/user/create", [UserController::class, 'create'])->name("users.create")
    ->middleware("auth");

Route::get("/user/edit/{id}", [UserController::class, 'edit'])->name("users.edit")
    ->middleware("auth");

Route::get("/user/show/{id}", [UserController::class, 'show'])->name("users.show")
    ->middleware("auth");

Route::delete("/user/show/{id}", [UserController::class, 'destroy'])->name("users.destroy")
    ->middleware("auth");

Route::PUT("/user", [UserController::class, 'update'])->name("users.update")
    ->middleware("auth");

Route::post("/user", [UserController::class, 'store'])->name("users.store")
    ->middleware("auth");

Route::post("/user/search", [UserController::class, 'search'])->name("users.search")
    ->middleware("auth");


require __DIR__.'/auth.php';
