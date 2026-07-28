<?php

use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
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
    Route::get('/student/dashboard', StudentDashboardController::class)
    ->middleware('role:student')
    ->name('student.dashboard');

    Route::view('/student/missions', 'student.coming-soon', [
    'title' => 'Misi Pembelajaran',
    'eyebrow' => 'MISI PEMBELAJARAN',
    'description' => 'Akses materi, latihan mikro, kuis, dan tantangan pembelajaran yang tersedia dalam setiap misi Questify.',
])
    ->middleware('role:student')
    ->name('student.missions.index');

Route::view('/student/progress', 'student.coming-soon', [
    'title' => 'Progress dan XP',
    'eyebrow' => 'PERKEMBANGAN BELAJAR',
    'description' => 'Pantau progres penyelesaian misi, jumlah XP, level, dan perkembangan perjalanan belajarmu.',
])
    ->middleware('role:student')
    ->name('student.progress.index');

Route::view('/student/badges', 'student.coming-soon', [
    'title' => 'Koleksi Badge',
    'eyebrow' => 'PENGHARGAAN',
    'description' => 'Lihat badge yang telah diperoleh dari penyelesaian materi, kuis, tantangan, dan misi pembelajaran.',
])
    ->middleware('role:student')
    ->name('student.badges.index');

Route::view('/student/portfolio', 'student.coming-soon', [
    'title' => 'E-Portfolio',
    'eyebrow' => 'PORTOFOLIO PEMBELAJARAN',
    'description' => 'Dokumentasikan hasil aktivitas, pencapaian, refleksi, dan perkembangan computational thinking selama belajar.',
])
    ->middleware('role:student')
    ->name('student.portfolio.index');
});

require __DIR__.'/settings.php';
