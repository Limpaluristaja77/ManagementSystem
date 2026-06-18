<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceRecordController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('users', [UserController::class, 'index'])->middleware('can:users.view')->name('users.index');
    Route::post('users', [UserController::class, 'store'])->middleware('can:users.create')->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->middleware('can:users.create')->name('users.update');
    Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->middleware('can:users.deactivate')->name('users.deactivate');
    Route::post('users/{user}/activate', [UserController::class, 'activate'])->middleware('can:users.deactivate')->name('users.activate');

    Route::get('roles', [RoleController::class, 'index'])->middleware('can:roles.view')->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->middleware('can:roles.create')->name('roles.store');
    Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('can:roles.create')->name('roles.update');
    Route::post('roles/{role}/deactivate', [RoleController::class, 'deactivate'])->middleware('can:roles.deactivate')->name('roles.deactivate');
    Route::post('roles/{role}/activate', [RoleController::class, 'activate'])->middleware('can:roles.deactivate')->name('roles.activate');

    Route::get('attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::post('attendance/check-in', [AttendanceRecordController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('attendance/check-out', [AttendanceRecordController::class, 'checkOut'])->name('attendance.check-out');
    Route::put('attendance/{attendanceRecord}', [AttendanceRecordController::class, 'update'])->name('attendance.update');
    Route::post('attendance/{attendanceRecord}/approve', [AttendanceRecordController::class, 'approve'])->name('attendance.approve');
    Route::post('attendance/{attendanceRecord}/reject', [AttendanceRecordController::class, 'reject'])->name('attendance.reject');
});

require __DIR__.'/settings.php';
