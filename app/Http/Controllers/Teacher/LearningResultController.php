<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\FinalChallengeAttempt;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PretestAttempt;
use App\Models\PosttestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LearningResultController extends Controller
{
    /**
     * Menampilkan rekap hasil pembelajaran peserta didik.
     */
    public function index(): View
    {
        /*
         * Mengambil seluruh peserta didik yang terdaftar.
         */
        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);

        $studentIds = $students->pluck('id');

        /*
         * Mengambil seluruh misi yang telah dipublikasikan.
         */
        $publishedMissions = Mission::query()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->get([
                'id',
                'title',
                'slug',
                'sequence',
            ]);

        /*
         * Untuk tahap awal, hasil pembelajaran berfokus
         * pada misi terbit pertama.
         */
        $focusedMission = $publishedMissions->first();

        $pretestAttempts = collect();
        $microExerciseAttempts = collect();
        $quizAttempts = collect();
        $finalChallengeAttempts = collect();
        $posttestAttempts = collect();

        if (
            $focusedMission !== null
            && $studentIds->isNotEmpty()
        ) {
            /*
             * Mengambil hasil Pretest terbaru setiap siswa.
             */
            $pretestAttempts = PretestAttempt::query()
                ->where(
                    'mission_id',
                    $focusedMission->id
                )
                ->whereIn('user_id', $studentIds)
->where(
    'status',
    PretestAttempt::STATUS_COMPLETED
)
->latest('completed_at')
                ->get()
                ->groupBy('user_id')
                ->map(
                    fn ($attempts) => $attempts->first()
                );

            /*
             * Mengambil hasil Latihan Mikro terbaru
             * yang telah diselesaikan.
             */
            $microExerciseAttempts =
                MicroExerciseAttempt::query()
                    ->where(
                        'mission_id',
                        $focusedMission->id
                    )
                    ->whereIn('user_id', $studentIds)
                    ->where(
                        'status',
                        MicroExerciseAttempt::STATUS_COMPLETED
                    )
                    ->latest('completed_at')
                    ->get()
                    ->groupBy('user_id')
                    ->map(
                        fn ($attempts) =>
                            $attempts->first()
                    );

            /*
             * Mengambil hasil Kuis Materi terbaru
             * yang telah diselesaikan.
             */
            $quizAttempts = QuizAttempt::query()
                ->where(
                    'mission_id',
                    $focusedMission->id
                )
                ->whereIn('user_id', $studentIds)
                ->where(
                    'status',
                    QuizAttempt::STATUS_COMPLETED
                )
                ->latest('completed_at')
                ->get()
                ->groupBy('user_id')
                ->map(
                    fn ($attempts) =>
                        $attempts->first()
                );

            /*
             * Mengambil hasil Final Challenge terbaru
             * yang telah diselesaikan.
             */
            $finalChallengeAttempts =
                FinalChallengeAttempt::query()
                    ->where(
                        'mission_id',
                        $focusedMission->id
                    )
                    ->whereIn('user_id', $studentIds)
                    ->where(
                        'status',
                        FinalChallengeAttempt::STATUS_COMPLETED
                    )
                    ->latest('completed_at')
                    ->get()
                    ->groupBy('user_id')
                    ->map(
                        fn ($attempts) =>
                            $attempts->first()
                    );

                    /*
 * Mengambil hasil Posttest terbaru
 * yang telah diselesaikan.
 */
$posttestAttempts = PosttestAttempt::query()
    ->where(
        'mission_id',
        $focusedMission->id
    )
    ->whereIn('user_id', $studentIds)
->where(
    'status',
    PosttestAttempt::STATUS_COMPLETED
)
->latest('completed_at')
    ->get()
    ->groupBy('user_id')
    ->map(
        fn ($attempts) =>
            $attempts->first()
    );
        }

        /*
         * Mengambil total XP dan jumlah misi selesai
         * untuk setiap peserta didik.
         */
        $missionStatistics =
            MissionCompletion::query()
                ->whereIn('user_id', $studentIds)
                ->select('user_id')
                ->selectRaw(
                    'COUNT(*) AS completed_missions'
                )
                ->selectRaw(
                    '
                    COALESCE(
                        SUM(earned_xp),
                        0
                    ) AS total_xp
                    '
                )
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

        /*
         * Menggabungkan hasil aktivitas menjadi satu
         * baris rekap untuk setiap peserta didik.
         */
        $learningResultRows = $students->map(
            function ($student) use (
    $pretestAttempts,
    $microExerciseAttempts,
    $quizAttempts,
    $finalChallengeAttempts,
    $posttestAttempts,
    $missionStatistics
): array {
                $pretestAttempt =
                    $pretestAttempts->get($student->id);

                $microExerciseAttempt =
                    $microExerciseAttempts->get(
                        $student->id
                    );

                $quizAttempt =
                    $quizAttempts->get($student->id);

                $finalChallengeAttempt =
                    $finalChallengeAttempts->get(
                        $student->id
                    );

                    $posttestAttempt =
    $posttestAttempts->get(
        $student->id
    );

                $missionStatistic =
                    $missionStatistics->get($student->id);

                $scores = collect([
                    $pretestAttempt !== null
                        ? (float) $pretestAttempt->score
                        : null,

                    $microExerciseAttempt !== null
                        ? (float) $microExerciseAttempt->score
                        : null,

                    $quizAttempt !== null
                        ? (float) $quizAttempt->score
                        : null,

                    $finalChallengeAttempt !== null
                        ? (float) $finalChallengeAttempt->score
                        : null,

                        $posttestAttempt !== null
    ? (float) $posttestAttempt->score
    : null,
                ])->filter(
                    fn ($score) => $score !== null
                );

                $isComplete =
    $pretestAttempt !== null
    && $microExerciseAttempt !== null
    && $quizAttempt !== null
    && $finalChallengeAttempt !== null
    && $posttestAttempt !== null;

                $averageScore = $scores->isNotEmpty()
                    ? round((float) $scores->average(), 2)
                    : null;

                    $learningGain =
    $pretestAttempt !== null
    && $posttestAttempt !== null
        ? round(
            (float) $posttestAttempt->score
            - (float) $pretestAttempt->score,
            2
        )
        : null;

                return [
                    'student' => $student,

                    'pretest_attempt' =>
                        $pretestAttempt,

                    'micro_exercise_attempt' =>
                        $microExerciseAttempt,

                    'quiz_attempt' =>
                        $quizAttempt,

                    'final_challenge_attempt' =>
                        $finalChallengeAttempt,

                        'posttest_attempt' =>
    $posttestAttempt,

                    'pretest_score' =>
                        $pretestAttempt !== null
                            ? (float) $pretestAttempt->score
                            : null,

                    'micro_exercise_score' =>
                        $microExerciseAttempt !== null
                            ? (float) $microExerciseAttempt->score
                            : null,

                    'quiz_score' =>
                        $quizAttempt !== null
                            ? (float) $quizAttempt->score
                            : null,

                    'final_challenge_score' =>
                        $finalChallengeAttempt !== null
                            ? (float) $finalChallengeAttempt->score
                            : null,

                            'posttest_score' =>
    $posttestAttempt !== null
        ? (float) $posttestAttempt->score
        : null,

                    'average_score' => $averageScore,
                    'learning_gain' => $learningGain,

                    'completed_activity_count' =>
                        $scores->count(),

                    'is_complete' => $isComplete,

                    'total_xp' => (int) (
                        $missionStatistic?->total_xp
                        ?? 0
                    ),

                    'completed_missions' => (int) (
                        $missionStatistic
                            ?->completed_missions
                        ?? 0
                    ),
                ];
            }
        );

        $studentCount = $learningResultRows->count();

        $completeResultCount = $learningResultRows
            ->where('is_complete', true)
            ->count();

        $incompleteResultCount =
    $focusedMission !== null
        ? max(
            0,
            $studentCount - $completeResultCount
        )
        : 0;

        /*
         * Menghitung rata-rata setiap aktivitas berdasarkan
         * nilai yang sudah tersedia.
         */
        $averagePretestScore = $this->averageScore(
            $learningResultRows,
            'pretest_score'
        );

        $averageMicroExerciseScore = $this->averageScore(
            $learningResultRows,
            'micro_exercise_score'
        );

        $averageQuizScore = $this->averageScore(
            $learningResultRows,
            'quiz_score'
        );

        $averageFinalChallengeScore =
            $this->averageScore(
                $learningResultRows,
                'final_challenge_score'
            );

            $averagePosttestScore = $this->averageScore(
    $learningResultRows,
    'posttest_score'
);

$averageLearningGain = $this->averageScore(
    $learningResultRows,
    'learning_gain'
);

        /*
         * Rata-rata keseluruhan dihitung berdasarkan
         * rata-rata siswa yang sudah memiliki nilai.
         */
        $overallAverageScore = $this->averageScore(
            $learningResultRows,
            'average_score'
        );

        return view('teacher.results.index', [
            'publishedMissions' =>
                $publishedMissions,

            'focusedMission' => $focusedMission,

            'learningResultRows' =>
                $learningResultRows,

            'studentCount' => $studentCount,

            'completeResultCount' =>
                $completeResultCount,

            'incompleteResultCount' =>
                $incompleteResultCount,

            'averagePretestScore' =>
                $averagePretestScore,

            'averageMicroExerciseScore' =>
                $averageMicroExerciseScore,

            'averageQuizScore' =>
                $averageQuizScore,

            'averageFinalChallengeScore' =>
                $averageFinalChallengeScore,

                'averagePosttestScore' =>
    $averagePosttestScore,

'averageLearningGain' =>
    $averageLearningGain,

            'overallAverageScore' =>
                $overallAverageScore,
        ]);
    }

    /**
     * Menghitung rata-rata kolom nilai tertentu.
     */
    private function averageScore(
        Collection $rows,
        string $column
    ): float {
        $scores = $rows
            ->pluck($column)
            ->filter(
                fn ($score) => $score !== null
            );

        if ($scores->isEmpty()) {
            return 0;
        }

        return round(
            (float) $scores->average(),
            2
        );
    }
}
