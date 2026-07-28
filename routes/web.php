<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
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
    Route::view('teacher/dashboard', 'teacher.dashboard')
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
