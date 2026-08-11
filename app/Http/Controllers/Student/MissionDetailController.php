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

class MissionDetailController extends Controller
{
    public function __invoke(
        Request $request,
        Mission $mission
    ): View {
        abort_unless($mission->is_published, 404);

        $userId = $request->user()->id;

        /*
         * Memeriksa penyelesaian Pretest.
         */
        $pretestCompleted = $mission
            ->pretestAttempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();

        /*
         * Mengambil seluruh blok materi wajib pada misi.
         */
        $requiredBlockIds = MaterialBlock::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->whereHas(
                'materialSection',
                function ($query) use ($mission): void {
                    $query
                        ->where('mission_id', $mission->id)
                        ->where('is_active', true);
                }
            )
            ->pluck('id');

        /*
         * Menghitung blok materi wajib yang telah selesai.
         */
        $completedRequiredBlocks = MaterialBlockProgress::query()
            ->where('user_id', $userId)
            ->whereIn('material_block_id', $requiredBlockIds)
            ->where('is_completed', true)
            ->count();

        $materialCompleted = $pretestCompleted
            && $requiredBlockIds->isNotEmpty()
            && $completedRequiredBlocks === $requiredBlockIds->count();

        /*
         * Memeriksa penyelesaian Latihan Mikro.
         */
        $microExerciseCompleted = MicroExerciseAttempt::query()
            ->where('mission_id', $mission->id)
            ->where('user_id', $userId)
            ->where(
                'status',
                MicroExerciseAttempt::STATUS_COMPLETED
            )
            ->exists();

        /*
         * Memeriksa penyelesaian Kuis Materi.
         */
        $quizCompleted = QuizAttempt::query()
            ->where('mission_id', $mission->id)
            ->where('user_id', $userId)
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
                ->where('mission_id', $mission->id)
                ->where('user_id', $userId)
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->exists();

        /*
         * Memeriksa penyelesaian Posttest.
         */
        $posttestCompleted = $mission
            ->posttestAttempts()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();

        $steps = [
            [
                'number' => 1,
                'key' => 'pretest',
                'title' => 'Pretest',
                'description' =>
                    'Kerjakan tes awal untuk mengukur kemampuan sebelum mempelajari materi.',
                'status' => $pretestCompleted
                    ? 'completed'
                    : 'available',
                'url' => route(
                    'student.missions.pretest.show',
                    ['mission' => $mission->slug]
                ),
            ],
            [
                'number' => 2,
                'key' => 'material',
                'title' => 'Materi Prakelas',
                'description' =>
    'Pelajari materi melalui bacaan bertahap dan checkpoint.',
                'status' => $materialCompleted
                    ? 'completed'
                    : (
                        $pretestCompleted
                            ? 'available'
                            : 'locked'
                    ),
                'url' => $pretestCompleted
                    ? route(
                        'student.missions.material.show',
                        ['mission' => $mission->slug]
                    )
                    : null,
            ],
            [
                'number' => 3,
                'key' => 'micro-exercise',
                'title' => 'Latihan Mikro',
                'description' =>
                    'Selesaikan latihan singkat yang tersedia setelah mempelajari materi.',
                'status' => $microExerciseCompleted
                    ? 'completed'
                    : (
                        $materialCompleted
                            ? 'available'
                            : 'locked'
                    ),
                'url' => $materialCompleted
                    ? route(
                        'student.missions.micro-exercise.show',
                        ['mission' => $mission->slug]
                    )
                    : null,
            ],
            [
                'number' => 4,
                'key' => 'quiz',
                'title' => 'Kuis Materi',
                'description' =>
                    'Uji pemahaman terhadap konsep yang telah dipelajari.',
                'status' => $quizCompleted
                    ? 'completed'
                    : (
                        $microExerciseCompleted
                            ? 'available'
                            : 'locked'
                    ),
                'url' => $microExerciseCompleted
                    ? route(
                        'student.missions.quiz.show',
                        ['mission' => $mission->slug]
                    )
                    : null,
            ],
            [
                'number' => 5,
                'key' => 'final-challenge',
                'title' => 'Final Challenge',
                'description' =>
                    'Terapkan pemahamanmu untuk menganalisis dan menyelesaikan permasalahan pemrograman.',
                'status' => $finalChallengeCompleted
                    ? 'completed'
                    : (
                        $quizCompleted
                            ? 'available'
                            : 'locked'
                    ),
                'url' => $quizCompleted
                    ? route(
                        'student.missions.final-challenge.show',
                        ['mission' => $mission->slug]
                    )
                    : null,
            ],
            [
                'number' => 6,
                'key' => 'posttest',
                'title' => 'Posttest',
                'description' =>
                    'Kerjakan tes akhir untuk mengukur kemampuan setelah menyelesaikan seluruh pembelajaran.',
                'status' => $posttestCompleted
                    ? 'completed'
                    : (
                        $finalChallengeCompleted
                            ? 'available'
                            : 'locked'
                    ),
                'url' => $finalChallengeCompleted
                    ? route(
                        'student.missions.posttest.show',
                        ['mission' => $mission->slug]
                    )
                    : null,
            ],
        ];

        return view('student.missions.show', [
            'mission' => $mission,
            'steps' => $steps,
        ]);
    }
}
