<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserChangePassword;
use App\Http\Controllers\BookReportController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', HomeController::class)->name('home');

Route::middleware('auth')->group(function() {
    Route::prefix('book')->name('books.')->group(function() {
        Route::get('create', [BookController::class, 'create'])->name('create');
        Route::post('store', [BookController::class, 'store'])->name('store');
        Route::get('{book:slug}/report/create', [BookReportController::class, 'create'])->name('report.create');
        Route::post('{book}/report', [BookReportController::class, 'store'])->name('report.store');
    });
    
    Route::prefix('user')->name('user.')->group(function() {
        Route::prefix('book')->name('books.')->group(function() {
            Route::get('/', [BookController::class, 'index'])->name('list');
            Route::get('{book:slug}/edit', [BookController::class, 'edit'])->name('edit');
            Route::put('{book:slug}', [BookController::class, 'update'])->name('update');
            Route::delete('{book}', [BookController::class, 'destroy'])->name('destroy');
        });

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('settings', [UserSettingsController::class, 'index'])->name('settings');
        Route::post('settings/{user}', [UserSettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/password/change/{user}', [UserChangePassword::class, 'update'])->name('password.update');
    });
});

Route::get('book/{book:slug}', [BookController::class, 'show'])->name('books.show');

Route::middleware('isAdmin')->prefix('admin')->name('admin.')->group(function() {
    Route::get('/', AdminDashboardController::class)->name('index');
    Route::resource('books', AdminBookController::class);
    Route::put('book/approve/{book}', [AdminBookController::class, 'approveBook'])->name('books.approve');
    Route::resource('users', AdminUsersController::class)->except(['create', 'store']);
});

require __DIR__ . '/auth.php';
