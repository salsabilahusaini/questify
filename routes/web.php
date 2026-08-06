<?php

use App\Http\Controllers\Student\MissionDetailController;
use App\Http\Controllers\Student\MissionController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\StudentMonitoringController;
use App\Http\Controllers\Teacher\LearningResultController;
use App\Http\Controllers\Teacher\MissionController as TeacherMissionController;
use App\Http\Controllers\Teacher\MaterialBlockController as TeacherMaterialBlockController;
use App\Http\Controllers\Teacher\MaterialSectionController as TeacherMaterialSectionController;
use App\Http\Controllers\Teacher\PretestQuestionController as TeacherPretestQuestionController;
use App\Http\Controllers\Teacher\PosttestQuestionController as TeacherPosttestQuestionController;
use App\Http\Controllers\Teacher\MicroExerciseQuestionController as TeacherMicroExerciseQuestionController;
use App\Http\Controllers\Teacher\QuizQuestionController as TeacherQuizQuestionController;
use App\Http\Controllers\Teacher\FinalChallengeController as TeacherFinalChallengeController;
use App\Models\User;
use App\Models\Mission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Student\PretestController;
use App\Http\Controllers\Student\PosttestController;
use App\Http\Controllers\Student\MaterialController;
use App\Http\Controllers\Student\MaterialProgressController;
use App\Http\Controllers\Student\MicroExerciseController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\FinalChallengeController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\BadgeController;
use App\Http\Controllers\Student\EPortfolioController;

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
        'totalMissions' => Mission::count(),
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

    Route::get(
    '/teacher/missions',
    [TeacherMissionController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.index');

    Route::get(
    '/teacher/missions/{mission}',
    [TeacherMissionController::class, 'show']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.show');

    Route::get(
    '/teacher/missions/{mission}/edit',
    [TeacherMissionController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.edit');

Route::put(
    '/teacher/missions/{mission}',
    [TeacherMissionController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.update');

    Route::get(
    '/teacher/missions/{mission}/material-sections/{section}/material-blocks/create',
    [
        TeacherMaterialBlockController::class,
        'create',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.material-blocks.create'
    );

    Route::post(
    '/teacher/missions/{mission}/material-sections/{section}/material-blocks',
    [
        TeacherMaterialBlockController::class,
        'store',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.material-blocks.store'
    );

    Route::get(
    '/teacher/missions/{mission}/material-blocks/{block}/edit',
    [TeacherMaterialBlockController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.material-blocks.edit');

Route::put(
    '/teacher/missions/{mission}/material-blocks/{block}',
    [TeacherMaterialBlockController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.material-blocks.update');

    Route::delete(
    '/teacher/missions/{mission}/material-blocks/{block}',
    [
        TeacherMaterialBlockController::class,
        'destroy',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.material-blocks.destroy'
    );

    Route::get(
    '/teacher/missions/{mission}/material-sections/create',
    [
        TeacherMaterialSectionController::class,
        'create',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.material-sections.create'
    );

    Route::post(
    '/teacher/missions/{mission}/material-sections',
    [
        TeacherMaterialSectionController::class,
        'store',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.material-sections.store'
    );

    Route::get(
    '/teacher/missions/{mission}/material-sections/{section}/edit',
    [TeacherMaterialSectionController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.material-sections.edit');

Route::put(
    '/teacher/missions/{mission}/material-sections/{section}',
    [TeacherMaterialSectionController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.material-sections.update');

    Route::delete(
    '/teacher/missions/{mission}/material-sections/{section}',
    [
        TeacherMaterialSectionController::class,
        'destroy',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.material-sections.destroy'
    );

    Route::get(
    '/teacher/missions/{mission}/pretest',
    [TeacherPretestQuestionController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.pretest.index');

    Route::get(
    '/teacher/missions/{mission}/pretest/create',
    [TeacherPretestQuestionController::class, 'create']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.pretest.create');

Route::post(
    '/teacher/missions/{mission}/pretest',
    [TeacherPretestQuestionController::class, 'store']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.pretest.store');

Route::get(
    '/teacher/missions/{mission}/pretest/{question}/edit',
    [TeacherPretestQuestionController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.pretest.edit');

Route::put(
    '/teacher/missions/{mission}/pretest/{question}',
    [TeacherPretestQuestionController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.pretest.update');

    Route::delete(
    '/teacher/missions/{mission}/pretest/{question}',
    [TeacherPretestQuestionController::class, 'destroy']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.pretest.destroy');

    Route::get(
    '/teacher/missions/{mission}/micro-exercises',
    [TeacherMicroExerciseQuestionController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.micro-exercises.index');

Route::get(
    '/teacher/missions/{mission}/micro-exercises/{question}/edit',
    [TeacherMicroExerciseQuestionController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.micro-exercises.edit');

    Route::get(
    '/teacher/missions/{mission}/micro-exercises/create',
    [TeacherMicroExerciseQuestionController::class, 'create']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.micro-exercises.create');

Route::post(
    '/teacher/missions/{mission}/micro-exercises',
    [TeacherMicroExerciseQuestionController::class, 'store']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.micro-exercises.store');

Route::put(
    '/teacher/missions/{mission}/micro-exercises/{question}',
    [TeacherMicroExerciseQuestionController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.micro-exercises.update');

    Route::delete(
    '/teacher/missions/{mission}/micro-exercises/{question}',
    [
        TeacherMicroExerciseQuestionController::class,
        'destroy',
    ]
)
    ->middleware('role:teacher')
    ->name(
        'teacher.missions.micro-exercises.destroy'
    );

    Route::get(
    '/teacher/missions/{mission}/quizzes',
    [TeacherQuizQuestionController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.quizzes.index');

    Route::get(
    '/teacher/missions/{mission}/quizzes/create',
    [TeacherQuizQuestionController::class, 'create']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.quizzes.create');

Route::post(
    '/teacher/missions/{mission}/quizzes',
    [TeacherQuizQuestionController::class, 'store']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.quizzes.store');

Route::get(
    '/teacher/missions/{mission}/quizzes/{question}/edit',
    [TeacherQuizQuestionController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.quizzes.edit');

Route::put(
    '/teacher/missions/{mission}/quizzes/{question}',
    [TeacherQuizQuestionController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.quizzes.update');

    Route::delete(
    '/teacher/missions/{mission}/quizzes/{question}',
    [TeacherQuizQuestionController::class, 'destroy']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.quizzes.destroy');

    Route::get(
    '/teacher/missions/{mission}/final-challenge',
    [TeacherFinalChallengeController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.final-challenge.index');

    Route::get(
    '/teacher/missions/{mission}/final-challenge/create',
    [TeacherFinalChallengeController::class, 'create']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.final-challenge.create');

    Route::post(
    '/teacher/missions/{mission}/final-challenge',
    [TeacherFinalChallengeController::class, 'store']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.final-challenge.store');

Route::get(
    '/teacher/missions/{mission}/final-challenge/{finalChallenge}/edit',
    [TeacherFinalChallengeController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.final-challenge.edit');

Route::put(
    '/teacher/missions/{mission}/final-challenge/{finalChallenge}',
    [TeacherFinalChallengeController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.final-challenge.update');

    Route::get(
    '/teacher/monitoring',
    [StudentMonitoringController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.monitoring.index');

    Route::get(
    '/teacher/monitoring/{student}',
    [StudentMonitoringController::class, 'show']
)
    ->middleware('role:teacher')
    ->name('teacher.monitoring.show');

    Route::get(
    '/teacher/results',
    [LearningResultController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.results.index');

    Route::get(
    '/teacher/missions/{mission}/posttest',
    [TeacherPosttestQuestionController::class, 'index']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.posttest.index');

    Route::get(
    '/teacher/missions/{mission}/posttest/create',
    [TeacherPosttestQuestionController::class, 'create']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.posttest.create');

Route::post(
    '/teacher/missions/{mission}/posttest',
    [TeacherPosttestQuestionController::class, 'store']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.posttest.store');

Route::get(
    '/teacher/missions/{mission}/posttest/{question}/edit',
    [TeacherPosttestQuestionController::class, 'edit']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.posttest.edit');

Route::put(
    '/teacher/missions/{mission}/posttest/{question}',
    [TeacherPosttestQuestionController::class, 'update']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.posttest.update');

    Route::delete(
    '/teacher/missions/{mission}/posttest/{question}',
    [TeacherPosttestQuestionController::class, 'destroy']
)
    ->middleware('role:teacher')
    ->name('teacher.missions.posttest.destroy');

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
    '/student/missions/{mission:slug}/posttest',
    [PosttestController::class, 'show']
)
    ->middleware('role:student')
    ->name('student.missions.posttest.show');

Route::post(
    '/student/missions/{mission:slug}/posttest/start',
    [PosttestController::class, 'start']
)
    ->middleware('role:student')
    ->name('student.missions.posttest.start');

Route::get(
    '/student/missions/{mission:slug}/posttest/questions',
    [PosttestController::class, 'questions']
)
    ->middleware('role:student')
    ->name('student.missions.posttest.questions');

Route::post(
    '/student/missions/{mission:slug}/posttest/submit',
    [PosttestController::class, 'submit']
)
    ->middleware('role:student')
    ->name('student.missions.posttest.submit');

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

Route::get(
    '/student/portfolio',
    [EPortfolioController::class, 'index']
)
    ->middleware('role:student')
    ->name('student.portfolio.index');
});

require __DIR__.'/settings.php';
