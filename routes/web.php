<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ReportController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::resource('products', ProductController::class)->except(['create', 'edit']);
    Route::resource('expenses', ExpenseController::class)->except(['create', 'edit']);
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos', [POSController::class, 'store'])->name('pos.store');
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
    Route::delete('/history/{id}', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::get('/struk/{id}', [POSController::class, 'struk']);
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/data', [ReportController::class, 'getData'])->name('report.data');
});
