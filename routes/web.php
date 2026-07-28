<?php

use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('admin')
    ->name('admin.')
    ->middleware('role:admin')
    ->group(function () {
        Route::resource('users', UserController::class)
            ->except(['show']);
    });
    /*
     * Mengarahkan pengguna ke dashboard sesuai role.
     */
    Route::get('dashboard', function (Request $request) {
        return match ($request->user()->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_TEACHER => redirect()->route('teacher.dashboard'),
            User::ROLE_STUDENT => redirect()->route('student.dashboard'),
            default => abort(403, 'Role pengguna tidak dikenali.'),
        };
    })->name('dashboard');

    /*
     * Dashboard khusus Admin.
     */
    Route::get('admin/dashboard', function () {
    return view('admin.dashboard', [
        'totalUsers' => User::count(),
        'totalTeachers' => User::where(
            'role',
            User::ROLE_TEACHER
        )->count(),
        'totalStudents' => User::where(
            'role',
            User::ROLE_STUDENT
        )->count(),
    ]);
})
    ->middleware('role:admin')
    ->name('admin.dashboard');

    /*
     * Dashboard khusus Guru.
     */
    Route::get('/teacher/dashboard', TeacherDashboardController::class)
    ->middleware('role:teacher')
    ->name('teacher.dashboard');

    /*
     * Dashboard khusus Peserta Didik.
     */
    Route::view('student/dashboard', 'student.dashboard')
        ->middleware('role:student')
        ->name('student.dashboard');
});

require __DIR__.'/settings.php';
