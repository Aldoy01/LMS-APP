<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\PaymentVerificationController as AdminPaymentVerificationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LmsDashboardController;
use App\Http\Controllers\PaymentConfirmationController;
use App\Http\Controllers\ParticipantDashboardController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [LmsDashboardController::class, 'index'])->name('lms.dashboard');
Route::get('/paket/{course:slug}/beli', [PurchaseController::class, 'create'])->name('purchase.create');
Route::post('/paket/{course:slug}/beli', [PurchaseController::class, 'store'])->name('purchase.store');
Route::get('/pembayaran/{invoice}', [PaymentConfirmationController::class, 'show'])->name('payments.show');
Route::post('/pembayaran/{invoice}/konfirmasi', [PaymentConfirmationController::class, 'confirm'])->name('payments.confirm');
Route::middleware(['auth', 'no.cache'])->group(function () {
    Route::get('/courses/{course:slug}', [LmsDashboardController::class, 'show'])->name('lms.courses.show');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.attempt');
});

Route::middleware(['auth', 'no.cache'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/home', [ParticipantDashboardController::class, 'index'])->name('participant.home');
    Route::get('/peserta/dashboard', [ParticipantDashboardController::class, 'index'])->name('participant.dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'no.cache', 'role:super-admin,admin-lms'])->group(function () {
    Route::resource('courses', AdminCourseController::class)->except(['show', 'destroy']);
    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
    Route::put('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('payments', [AdminPaymentVerificationController::class, 'index'])->name('payments.index');
    Route::put('payments/{order}/verify', [AdminPaymentVerificationController::class, 'verify'])->name('payments.verify');
});
