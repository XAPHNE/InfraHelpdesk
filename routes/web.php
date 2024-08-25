<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'restrict.employee'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Only allow ticket management for employees
    Route::middleware('restrict.employee')->group(function () {
        Route::resource('ticket-management', TicketController::class);
    });

    // Admins and Vendors only for User Management
    Route::middleware('restrict.employee')->group(function () {
        Route::resource('user-management', UserController::class);
        Route::post('ticket-management/action-taken', [TicketController::class, 'storeActionTaken'])->name('action-taken.store');
        Route::get('ticket-management/{id}/details', [TicketController::class, 'show'])->name('ticket-management.details');
        Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');
    });
});

require __DIR__.'/auth.php';
