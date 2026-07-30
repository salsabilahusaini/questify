<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\FinalChallengeAttempt;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PretestAttempt;
use App\Models\QuizAttempt;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EPortfolioController extends Controller
{
    /**
     * Menampilkan rangkuman perjalanan belajar siswa
     * dalam bentuk E-Portfolio.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
         * Mengambil seluruh misi yang telah diselesaikan siswa.
         */
        $completions = MissionCompletion::query()
            ->where('user_id', $user->id)
            ->with([
                'mission:id,title,slug,xp_reward',
            ])
            ->latest('completed_at')
            ->get();

        /*
         * Menghitung jumlah XP berdasarkan penyelesaian misi.
         */
        $totalXp = (int) $completions->sum('earned_xp');

        /*
         * Menghitung jumlah misi yang telah diselesaikan.
         */
        $completedMissions = $completions->count();

        /*
         * Menghitung seluruh misi yang dipublikasikan.
         */
        $totalPublishedMissions = Mission::query()
            ->where('is_published', true)
            ->count();

        /*
         * Menghitung persentase penyelesaian seluruh misi.
         */
        $missionProgressPercentage =
            $totalPublishedMissions > 0
                ? round(
                    (
                        $completedMissions
                        / $totalPublishedMissions
                    ) * 100,
                    2
                )
                : 0;

        /*
         * Mengambil seluruh badge yang telah diperoleh siswa.
         */
        $userBadges = UserBadge::query()
            ->where('user_id', $user->id)
            ->with('badge')
            ->latest('awarded_at')
            ->get();

        $earnedBadgeCount = $userBadges->count();

        /*
         * Menghitung seluruh badge yang aktif.
         */
        $totalActiveBadges = Badge::query()
            ->where('is_active', true)
            ->count();

        /*
         * Mengambil nilai Final Challenge tertinggi siswa.
         */
        $highestChallengeScore = (float) (
            FinalChallengeAttempt::query()
                ->where('user_id', $user->id)
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->max('score') ?? 0
        );

        /*
         * Mengambil hasil Pretest terbaru pada setiap misi.
         *
         * Collection diindeks menggunakan mission_id agar hasilnya
         * dapat dipasangkan dengan misi pada halaman E-Portfolio.
         */
        $pretestAttempts = PretestAttempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->get()
            ->groupBy('mission_id')
            ->map(function ($attempts) {
                return $attempts->first();
            });

        /*
         * Mengambil hasil Latihan Mikro terbaru pada setiap misi.
         */
        $microExerciseAttempts = MicroExerciseAttempt::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                MicroExerciseAttempt::STATUS_COMPLETED
            )
            ->latest('completed_at')
            ->get()
            ->groupBy('mission_id')
            ->map(function ($attempts) {
                return $attempts->first();
            });

        /*
         * Mengambil hasil Kuis Materi terbaru pada setiap misi.
         */
        $quizAttempts = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                QuizAttempt::STATUS_COMPLETED
            )
            ->latest('completed_at')
            ->get()
            ->groupBy('mission_id')
            ->map(function ($attempts) {
                return $attempts->first();
            });

        /*
         * Mengambil hasil Final Challenge terbaru pada setiap misi.
         */
        $finalChallengeAttempts =
            FinalChallengeAttempt::query()
                ->where('user_id', $user->id)
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->latest('completed_at')
                ->get()
                ->groupBy('mission_id')
                ->map(function ($attempts) {
                    return $attempts->first();
                });

        /*
         * Menentukan level berdasarkan total XP.
         *
         * Starter      : 0–99 XP
         * Beginner     : 100–299 XP
         * Intermediate : 300–599 XP
         * Advanced     : 600 XP atau lebih
         */
        if ($totalXp < 100) {
            $level = 'Starter';
        } elseif ($totalXp < 300) {
            $level = 'Beginner';
        } elseif ($totalXp < 600) {
            $level = 'Intermediate';
        } else {
            $level = 'Advanced';
        }

        return view('student.eportfolio.index', [
            'user' => $user,
            'completions' => $completions,
            'totalXp' => $totalXp,
            'completedMissions' => $completedMissions,
            'totalPublishedMissions' =>
                $totalPublishedMissions,
            'missionProgressPercentage' =>
                $missionProgressPercentage,
            'userBadges' => $userBadges,
            'earnedBadgeCount' => $earnedBadgeCount,
            'totalActiveBadges' => $totalActiveBadges,
            'highestChallengeScore' =>
                $highestChallengeScore,
            'pretestAttempts' => $pretestAttempts,
            'microExerciseAttempts' =>
                $microExerciseAttempts,
            'quizAttempts' => $quizAttempts,
            'finalChallengeAttempts' =>
                $finalChallengeAttempts,
            'level' => $level,
        ]);
    }
}
