<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\ModuleMaterialController as AdminModuleMaterialController;
use App\Http\Controllers\Admin\PaymentVerificationController as AdminPaymentVerificationController;
use App\Http\Controllers\Admin\SiteContentController as AdminSiteContentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LessonPageController;
use App\Http\Controllers\LmsDashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PaymentConfirmationController;
use App\Http\Controllers\ParticipantDashboardController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SiteMediaController;
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
Route::get('/program', [PublicPageController::class, 'programs'])->name('programs.index');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/site-media/{filename}', [SiteMediaController::class, 'show'])->name('site-media.show');
Route::get('/register', [PurchaseController::class, 'register'])->name('register');
Route::get('/paket/{course:slug}/beli', [PurchaseController::class, 'create'])->name('purchase.create');
Route::post('/paket/{course:slug}/beli', [PurchaseController::class, 'store'])->name('purchase.store');
Route::get('/pembayaran/{invoice}', [PaymentConfirmationController::class, 'show'])->name('payments.show');
Route::post('/pembayaran/{invoice}/konfirmasi', [PaymentConfirmationController::class, 'confirm'])->name('payments.confirm');
Route::middleware(['auth', 'no.cache'])->group(function () {
    Route::get('/courses/{course:slug}', [LmsDashboardController::class, 'show'])->name('lms.courses.show');
    Route::get('/courses/{course:slug}/lessons/{lesson}', [LessonPageController::class, 'show'])->name('lms.lessons.show');
    Route::post('/courses/{course:slug}/lessons/{lesson}/complete', [LessonPageController::class, 'complete'])->name('lms.lessons.complete');
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.attempt');
});

Route::middleware(['auth', 'no.cache'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/paket/{course:slug}/order', [PurchaseController::class, 'order'])->name('purchase.order');
    Route::get('/home', [ParticipantDashboardController::class, 'index'])->name('participant.home');
    Route::get('/peserta/dashboard', [ParticipantDashboardController::class, 'index'])->name('participant.dashboard');
    Route::get('/peserta/profile', [ParticipantDashboardController::class, 'profile'])->name('participant.profile');
    Route::get('/peserta/avatar', [ParticipantDashboardController::class, 'avatar'])->name('participant.avatar');
    Route::put('/peserta/profile', [ParticipantDashboardController::class, 'updateProfile'])->name('participant.profile.update');
    Route::put('/peserta/password', [ParticipantDashboardController::class, 'updatePassword'])->name('participant.password.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'no.cache', 'role:super-admin,admin-lms'])->group(function () {
    Route::get('site-content', [AdminSiteContentController::class, 'edit'])->name('site-content.edit');
    Route::put('site-content', [AdminSiteContentController::class, 'update'])->name('site-content.update');
    Route::resource('courses', AdminCourseController::class)->except(['show', 'destroy']);
    Route::get('courses/{course:slug}/materials', [AdminModuleMaterialController::class, 'index'])->name('courses.materials.index');
    Route::post('courses/{course:slug}/modules', [AdminModuleMaterialController::class, 'storeModule'])->name('modules.store');
    Route::put('modules/{module}', [AdminModuleMaterialController::class, 'updateModule'])->name('modules.update');
    Route::post('modules/{module}/lessons', [AdminModuleMaterialController::class, 'storeLesson'])->name('lessons.store');
    Route::post('lessons/{lesson}/materials', [AdminModuleMaterialController::class, 'storeMaterial'])->name('materials.store');
    Route::put('materials/{material}', [AdminModuleMaterialController::class, 'updateMaterial'])->name('materials.update');
    Route::delete('materials/{material}', [AdminModuleMaterialController::class, 'destroyMaterial'])->name('materials.destroy');
    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
    Route::put('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('payments', [AdminPaymentVerificationController::class, 'index'])->name('payments.index');
    Route::put('payments/{order}/verify', [AdminPaymentVerificationController::class, 'verify'])->name('payments.verify');
});
