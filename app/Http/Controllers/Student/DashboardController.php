<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalChallengeAttempt;
use App\Models\MaterialBlock;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\QuizAttempt;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard peserta didik.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        /*
         * Menghitung pencapaian keseluruhan peserta didik.
         */
        $completedMissionCount = MissionCompletion::query()
            ->where('user_id', $user->id)
            ->count();

        $totalXp = (int) MissionCompletion::query()
            ->where('user_id', $user->id)
            ->sum('earned_xp');

        $badgeCount = UserBadge::query()
            ->where('user_id', $user->id)
            ->count();

        $currentLevel = match (true) {
            $totalXp >= 600 => 'Advanced',
            $totalXp >= 300 => 'Intermediate',
            $totalXp >= 100 => 'Beginner',
            default => 'Starter',
        };

        /*
         * Misi aktif ditentukan berdasarkan misi yang telah
         * dipublikasikan dan memiliki urutan paling awal.
         */
        $activeMission = Mission::query()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->first();

        $missionProgress = 0;
        $pretestCompleted = false;
        $materialCompleted = false;
        $microPracticeCompleted = false;
        $quizCompleted = false;
        $finalChallengeCompleted = false;

        if ($activeMission !== null) {
            /*
             * Memeriksa penyelesaian Pretest.
             */
            $pretestAttempt = $activeMission
                ->pretestAttempts()
                ->where('user_id', $user->id)
                ->first();

            $pretestCompleted =
                $pretestAttempt?->status === 'completed';

            /*
             * Mengambil seluruh blok materi wajib
             * pada misi aktif.
             */
            $requiredBlockIds = MaterialBlock::query()
                ->where('is_active', true)
                ->where('is_required', true)
                ->whereHas(
                    'materialSection',
                    function ($query) use ($activeMission) {
                        $query
                            ->where(
                                'mission_id',
                                $activeMission->id
                            )
                            ->where('is_active', true);
                    }
                )
                ->pluck('id');

            $completedRequiredBlocks =
                MaterialBlockProgress::query()
                    ->where('user_id', $user->id)
                    ->whereIn(
                        'material_block_id',
                        $requiredBlockIds
                    )
                    ->where('is_completed', true)
                    ->count();

            $materialCompleted =
                $pretestCompleted
                && $requiredBlockIds->isNotEmpty()
                && $completedRequiredBlocks
                    === $requiredBlockIds->count();

            /*
             * Memeriksa penyelesaian Latihan Mikro.
             */
            $microPracticeCompleted =
                MicroExerciseAttempt::query()
                    ->where(
                        'mission_id',
                        $activeMission->id
                    )
                    ->where('user_id', $user->id)
                    ->where(
                        'status',
                        MicroExerciseAttempt::STATUS_COMPLETED
                    )
                    ->exists();

            /*
             * Memeriksa penyelesaian Kuis Materi.
             */
            $quizCompleted = QuizAttempt::query()
                ->where(
                    'mission_id',
                    $activeMission->id
                )
                ->where('user_id', $user->id)
                ->where(
                    'status',
                    QuizAttempt::STATUS_COMPLETED
                )
                ->exists();

            /*
             * Memeriksa penyelesaian Final Challenge.
             */
            $finalChallengeCompleted =
                FinalChallengeAttempt::query()
                    ->where(
                        'mission_id',
                        $activeMission->id
                    )
                    ->where('user_id', $user->id)
                    ->where(
                        'status',
                        FinalChallengeAttempt::STATUS_COMPLETED
                    )
                    ->exists();

            /*
             * Progres misi dihitung dari lima tahap:
             * Pretest, Materi, Latihan Mikro, Kuis,
             * dan Final Challenge.
             */
            $completedStepCount = count(
                array_filter([
                    $pretestCompleted,
                    $materialCompleted,
                    $microPracticeCompleted,
                    $quizCompleted,
                    $finalChallengeCompleted,
                ])
            );

            $missionProgress = (int) round(
                ($completedStepCount / 5) * 100
            );
        }

        return view('student.dashboard', [
            'completedMissionCount' =>
                $completedMissionCount,
            'totalXp' => $totalXp,
            'currentLevel' => $currentLevel,
            'badgeCount' => $badgeCount,
            'missionProgress' => $missionProgress,
            'activeMission' => $activeMission,
            'pretestCompleted' => $pretestCompleted,
            'materialCompleted' => $materialCompleted,
            'microPracticeCompleted' =>
                $microPracticeCompleted,
            'quizCompleted' => $quizCompleted,
        ]);
    }
}
