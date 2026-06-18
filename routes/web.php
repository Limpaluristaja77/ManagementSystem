<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceRecordController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::inertia('roles', 'Roles')->name('roles.index');

    Route::get('attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::post('attendance/check-in', [AttendanceRecordController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('attendance/check-out', [AttendanceRecordController::class, 'checkOut'])->name('attendance.check-out');
    Route::put('attendance/{attendanceRecord}', [AttendanceRecordController::class, 'update'])->name('attendance.update');
    Route::post('attendance/{attendanceRecord}/approve', [AttendanceRecordController::class, 'approve'])->name('attendance.approve');
    Route::post('attendance/{attendanceRecord}/reject', [AttendanceRecordController::class, 'reject'])->name('attendance.reject');
});

require __DIR__.'/settings.php';
