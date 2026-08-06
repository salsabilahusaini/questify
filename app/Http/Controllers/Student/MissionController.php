<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalChallengeAttempt;
use App\Models\MaterialBlock;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MissionController extends Controller
{
    public function __invoke(Request $request): View
    {
        $userId = $request->user()->id;
        $totalSteps = 6;

        $missions = Mission::query()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->get()
            ->each(
                function (Mission $mission) use (
                    $userId,
                    $totalSteps
                ): void {
                    $pretestCompleted = $mission
                        ->pretestAttempts()
                        ->where('user_id', $userId)
                        ->where('status', 'completed')
                        ->exists();

                    $requiredBlockIds = MaterialBlock::query()
                        ->where('is_active', true)
                        ->where('is_required', true)
                        ->whereHas(
                            'materialSection',
                            function ($query) use (
                                $mission
                            ): void {
                                $query
                                    ->where(
                                        'mission_id',
                                        $mission->id
                                    )
                                    ->where(
                                        'is_active',
                                        true
                                    );
                            }
                        )
                        ->pluck('id');

                    $completedRequiredBlocks =
                        MaterialBlockProgress::query()
                            ->where('user_id', $userId)
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

                    $microExerciseCompleted =
                        MicroExerciseAttempt::query()
                            ->where(
                                'mission_id',
                                $mission->id
                            )
                            ->where('user_id', $userId)
                            ->where(
                                'status',
                                MicroExerciseAttempt::STATUS_COMPLETED
                            )
                            ->exists();

                    $quizCompleted = QuizAttempt::query()
                        ->where(
                            'mission_id',
                            $mission->id
                        )
                        ->where('user_id', $userId)
                        ->where(
                            'status',
                            QuizAttempt::STATUS_COMPLETED
                        )
                        ->exists();

                    $finalChallengeCompleted =
                        FinalChallengeAttempt::query()
                            ->where(
                                'mission_id',
                                $mission->id
                            )
                            ->where('user_id', $userId)
                            ->where(
                                'status',
                                FinalChallengeAttempt::STATUS_COMPLETED
                            )
                            ->exists();

                    $posttestCompleted = $mission
                        ->posttestAttempts()
                        ->where('user_id', $userId)
                        ->where('status', 'completed')
                        ->exists();

                    $completedSteps = collect([
                        $pretestCompleted,
                        $materialCompleted,
                        $microExerciseCompleted,
                        $quizCompleted,
                        $finalChallengeCompleted,
                        $posttestCompleted,
                    ])->filter()->count();

                    $progressPercentage = (int) round(
                        ($completedSteps / $totalSteps) * 100
                    );

                    $progressLabel = match (true) {
                        $completedSteps === 0 =>
                            'Belum dimulai',
                        $completedSteps === $totalSteps =>
                            'Selesai',
                        default =>
                            'Sedang berlangsung',
                    };

                    $mission->setAttribute(
                        'completed_steps',
                        $completedSteps
                    );

                    $mission->setAttribute(
                        'total_steps',
                        $totalSteps
                    );

                    $mission->setAttribute(
                        'progress_percentage',
                        $progressPercentage
                    );

                    $mission->setAttribute(
                        'progress_label',
                        $progressLabel
                    );
                }
            );

        return view('student.missions.index', [
            'missions' => $missions,
        ]);
    }
}
