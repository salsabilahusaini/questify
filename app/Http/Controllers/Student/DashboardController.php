<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard peserta didik.
     */
    public function __invoke(): View
    {
        /*
         * Data perkembangan masih menggunakan nilai awal karena
         * tabel misi, XP, badge, dan aktivitas pembelajaran belum dibuat.
         */
        return view('student.dashboard', [
            'completedMissionCount' => 0,
            'totalXp' => 0,
            'currentLevel' => 'Starter',
            'badgeCount' => 0,
            'missionProgress' => 0,

            'pretestCompleted' => false,
            'materialCompleted' => false,
            'microPracticeCompleted' => false,
            'quizCompleted' => false,
        ]);
    }
}
