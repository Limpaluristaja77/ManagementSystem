<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceRecordController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::inertia('users', 'Users')->name('users.index');
    Route::inertia('roles', 'Roles')->name('roles.index');

    Route::get('attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::post('attendance/check-in', [AttendanceRecordController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('attendance/check-out', [AttendanceRecordController::class, 'checkOut'])->name('attendance.check-out');
    Route::put('attendance/{attendanceRecord}', [AttendanceRecordController::class, 'update'])->name('attendance.update');
    Route::post('attendance/{attendanceRecord}/approve', [AttendanceRecordController::class, 'approve'])->name('attendance.approve');
    Route::post('attendance/{attendanceRecord}/reject', [AttendanceRecordController::class, 'reject'])->name('attendance.reject');
});

require __DIR__.'/settings.php';
