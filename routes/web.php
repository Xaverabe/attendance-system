<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceImportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'welcome'])->name('welcome');
Route::post('/attendance/quick-store', [AttendanceController::class, 'quickStore'])->name('attendance.quickStore');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/classes', [ClassRoomController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassRoomController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassRoomController::class, 'store'])->name('classes.store');
    Route::get('/classes/{classroom}/edit', [ClassRoomController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{classroom}', [ClassRoomController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{classroom}', [ClassRoomController::class, 'destroy'])->name('classes.destroy');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/class/{classroom}', [AttendanceController::class, 'createForClass'])->name('attendance.create');
    Route::post('/attendance/class/{classroom}', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/student/{student}', [AttendanceController::class, 'show'])->name('attendance.student');

    // Excel Template Download & Bulk Attendance Import
    Route::get('/attendance/import', [AttendanceImportController::class, 'showImportForm'])->name('attendance.import');
    Route::post('/attendance/import', [AttendanceImportController::class, 'import'])->name('attendance.import.submit');
    Route::get('/attendance/template/{classroom}', [AttendanceImportController::class, 'downloadTemplate'])->name('attendance.template');

    // Table Exports (Excel/CSV)
    Route::get('/export/dashboard-students', [ExportController::class, 'dashboardStudents'])->name('export.dashboard.students');
    Route::get('/export/class-overview', [ExportController::class, 'classOverview'])->name('export.class.overview');
    Route::get('/export/students', [ExportController::class, 'students'])->name('export.students');
    Route::get('/export/attendance', [ExportController::class, 'attendance'])->name('export.attendance');
    Route::get('/export/attendance/student/{student}', [ExportController::class, 'studentAttendance'])->name('export.attendance.student');
});
