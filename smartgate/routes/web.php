<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Office\DashboardController as OfficeDashboard;
use App\Http\Controllers\Guard\DashboardController as GuardDashboard;

// Landing & Public Routes
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');
Route::get('/online-registration', [App\Http\Controllers\LandingController::class, 'showRegistrationForm'])->name('online-registration');
Route::post('/online-registration', [App\Http\Controllers\LandingController::class, 'submitRegistration'])->name('online-registration.submit');
Route::post('/online-registration/validate-document', [App\Http\Controllers\LandingController::class, 'validateDocument'])->name('online-registration.validate');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboard::class, 'users'])->name('users');
    Route::post('/users', [AdminDashboard::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [AdminDashboard::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminDashboard::class, 'deleteUser'])->name('users.delete');
    Route::get('/rfid', [AdminDashboard::class, 'rfid'])->name('rfid');
    Route::post('/rfid/{id}/toggle-status', [AdminDashboard::class, 'toggleStatus'])->name('rfid.toggle-status');
    Route::get('/rfid/{id}', [AdminDashboard::class, 'showRegistration'])->name('rfid.show');
    Route::get('/reports', [AdminDashboard::class, 'reports'])->name('reports');
    Route::get('/settings', [AdminDashboard::class, 'settings'])->name('settings');
});

// Office Routes
Route::middleware(['role:office'])->prefix('office')->name('office.')->group(function () {
    Route::get('/dashboard', [OfficeDashboard::class, 'index'])->name('dashboard');
    Route::get('/registration', [OfficeDashboard::class, 'registration'])->name('registration');
    Route::post('/registration', [OfficeDashboard::class, 'store'])->name('registration.store');
    Route::get('/registration/{id}', [OfficeDashboard::class, 'show'])->name('registration.show');
    Route::put('/registration/{id}', [OfficeDashboard::class, 'update'])->name('registration.update');
    Route::delete('/registration/{id}', [OfficeDashboard::class, 'destroy'])->name('registration.destroy');
    Route::get('/users', [OfficeDashboard::class, 'users'])->name('users');
    Route::get('/stats', [OfficeDashboard::class, 'stats'])->name('stats');
    Route::get('/check-tag', [OfficeDashboard::class, 'checkTag'])->name('registration.checkTag');
    Route::post('/registration/{id}/verify', [OfficeDashboard::class, 'verify'])->name('registration.verify');
    Route::post('/registration/{id}/reject', [OfficeDashboard::class, 'reject'])->name('registration.reject');
});

// Guard Routes
Route::middleware(['role:guard'])->prefix('guard')->name('guard.')->group(function () {
    Route::get('/dashboard', [GuardDashboard::class, 'index'])->name('dashboard');
    Route::get('/entry', [GuardDashboard::class, 'entry'])->name('entry');
    Route::get('/exit', [GuardDashboard::class, 'exit'])->name('exit');
    
    // Hardware integration routes
    Route::get('/lookup-tag', [GuardDashboard::class, 'lookupTag'])->name('lookup.tag');
    Route::post('/log-vehicle', [GuardDashboard::class, 'logVehicle'])->name('log.vehicle');

    // Visitor manual entry routes
    Route::post('/visitor-entry', [GuardDashboard::class, 'storeVisitor'])->name('visitor.store');
    Route::post('/visitor-exit/{id}', [GuardDashboard::class, 'exitVisitor'])->name('visitor.exit.process');
});
