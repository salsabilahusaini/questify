<?php

use App\Http\Controllers\Student\MissionDetailController;
use App\Http\Controllers\Student\MissionController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Student\PretestController;
use App\Http\Controllers\Student\MaterialController;
use App\Http\Controllers\Student\MaterialProgressController;
use App\Http\Controllers\Student\MicroExerciseController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\FinalChallengeController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\BadgeController;

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

    Route::get('/student/missions', MissionController::class)
    ->middleware('role:student')
    ->name('student.missions.index');

    Route::get(
    '/student/missions/{mission:slug}',
    MissionDetailController::class
)
    ->middleware('role:student')
    ->name('student.missions.show');

    Route::get(
    '/student/missions/{mission:slug}/pretest',
    [PretestController::class, 'show']
)
    ->middleware('role:student')
    ->name('student.missions.pretest.show');

    Route::post(
    '/student/missions/{mission:slug}/pretest/start',
    [PretestController::class, 'start']
)
    ->middleware('role:student')
    ->name('student.missions.pretest.start');

    Route::get(
    '/student/missions/{mission:slug}/pretest/questions',
    [PretestController::class, 'questions']
)
    ->middleware('role:student')
    ->name('student.missions.pretest.questions');

    Route::post(
    '/student/missions/{mission:slug}/pretest/submit',
    [PretestController::class, 'submit']
)
    ->middleware('role:student')
    ->name('student.missions.pretest.submit');

    Route::get(
    '/student/missions/{mission:slug}/material',
    [MaterialController::class, 'show']
)
    ->middleware('role:student')
    ->name('student.missions.material.show');

    Route::get(
    '/student/missions/{mission:slug}/micro-exercise',
    [MicroExerciseController::class, 'show']
)
    ->middleware('role:student')
    ->name('student.missions.micro-exercise.show');

    Route::post(
    '/student/missions/{mission:slug}/micro-exercise/submit',
    [MicroExerciseController::class, 'submit']
)
    ->middleware('role:student')
    ->name('student.missions.micro-exercise.submit');

    Route::get(
    '/student/missions/{mission:slug}/quiz',
    [QuizController::class, 'show']
)
    ->middleware('role:student')
    ->name('student.missions.quiz.show');

    Route::post(
    '/student/missions/{mission:slug}/quiz/submit',
    [QuizController::class, 'submit']
)
    ->middleware('role:student')
    ->name('student.missions.quiz.submit');

    Route::get(
    '/student/missions/{mission:slug}/final-challenge',
    [FinalChallengeController::class, 'show']
)
    ->middleware('role:student')
    ->name('student.missions.final-challenge.show');

    Route::post(
    '/student/missions/{mission:slug}/final-challenge/submit',
    [FinalChallengeController::class, 'submit']
)
    ->middleware('role:student')
    ->name('student.missions.final-challenge.submit');

    Route::post(
    '/student/missions/{mission:slug}/material/blocks/{block}/open',
    [MaterialProgressController::class, 'open']
)
    ->middleware('role:student')
    ->name('student.missions.material.blocks.open');

Route::post(
    '/student/missions/{mission:slug}/material/blocks/{block}/heartbeat',
    [MaterialProgressController::class, 'heartbeat']
)
    ->middleware('role:student')
    ->name('student.missions.material.blocks.heartbeat');

Route::get(
    '/student/progress',
    [ProgressController::class, 'index']
)
    ->middleware('role:student')
    ->name('student.progress.index');

Route::get(
    '/student/badges',
    [BadgeController::class, 'index']
)
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
