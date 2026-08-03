<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\FinalChallengeAttempt;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PretestAttempt;
use App\Models\PosttestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StudentMonitoringController extends Controller
{
    /**
     * Menampilkan daftar monitoring aktivitas peserta didik.
     */
    public function index(): View
    {
        /*
         * Mengambil seluruh akun peserta didik.
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
         * Mengambil seluruh misi yang dipublikasikan.
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
         * Untuk tahap awal, halaman monitoring berfokus pada
         * misi pertama berdasarkan urutan sequence.
         */
        $focusedMission = $publishedMissions->first();

        $requiredMaterialBlockIds = collect();
        $requiredMaterialBlockCount = 0;

        $pretestAttempts = collect();
        $materialProgresses = collect();
        $microExerciseAttempts = collect();
        $quizAttempts = collect();

        if (
            $focusedMission !== null
            && $studentIds->isNotEmpty()
        ) {
            /*
             * Mengambil seluruh blok materi aktif dan wajib
             * pada misi yang sedang dipantau.
             */
            $requiredMaterialBlockIds =
                MaterialBlock::query()
                    ->where('is_active', true)
                    ->where('is_required', true)
                    ->whereHas(
                        'materialSection',
                        function ($query) use (
                            $focusedMission
                        ): void {
                            $query
                                ->where(
                                    'mission_id',
                                    $focusedMission->id
                                )
                                ->where('is_active', true);
                        }
                    )
                    ->pluck('id');

            $requiredMaterialBlockCount =
                $requiredMaterialBlockIds->count();

            /*
             * Mengambil pengerjaan Pretest terbaru
             * setiap siswa.
             */
            $pretestAttempts =
                PretestAttempt::query()
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
                        fn ($attempts) =>
                            $attempts->first()
                    );

            /*
             * Merangkum progres membaca materi:
             * - blok selesai;
             * - waktu membaca;
             * - jumlah kunjungan;
             * - aktivitas terakhir.
             */
            if ($requiredMaterialBlockIds->isNotEmpty()) {
                $materialProgresses =
                    MaterialBlockProgress::query()
                        ->whereIn('user_id', $studentIds)
                        ->whereIn(
                            'material_block_id',
                            $requiredMaterialBlockIds
                        )
                        ->select('user_id')
                        ->selectRaw(
                            '
                            COUNT(
                                DISTINCT CASE
                                    WHEN is_completed = 1
                                    THEN material_block_id
                                END
                            ) AS completed_blocks
                            '
                        )
                        ->selectRaw(
                            '
                            COALESCE(
                                SUM(total_read_seconds),
                                0
                            ) AS total_read_seconds
                            '
                        )
                        ->selectRaw(
                            '
                            COALESCE(
                                SUM(visit_count),
                                0
                            ) AS total_visits
                            '
                        )
                        ->selectRaw(
                            '
                            MAX(last_activity_at)
                            AS last_activity_at
                            '
                        )
                        ->groupBy('user_id')
                        ->get()
                        ->keyBy('user_id');
            }

            /*
             * Mengambil Latihan Mikro terbaru
             * yang telah selesai.
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
             * Mengambil Kuis Materi terbaru
             * yang telah selesai.
             */
            $quizAttempts =
                QuizAttempt::query()
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
        }

        /*
         * Mengambil jumlah misi selesai dan total XP
         * seluruh siswa.
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
         * Mengambil jumlah badge setiap siswa.
         */
        $badgeStatistics =
            UserBadge::query()
                ->whereIn('user_id', $studentIds)
                ->select('user_id')
                ->selectRaw(
                    'COUNT(*) AS earned_badges'
                )
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

        /*
         * Menggabungkan seluruh data aktivitas menjadi
         * satu baris monitoring untuk setiap siswa.
         */
        $studentMonitoringRows = $students->map(
            function ($student) use (
                $pretestAttempts,
                $materialProgresses,
                $microExerciseAttempts,
                $quizAttempts,
                $missionStatistics,
                $badgeStatistics,
                $requiredMaterialBlockCount
            ): array {
                $pretestAttempt =
                    $pretestAttempts->get($student->id);

                $materialProgress =
                    $materialProgresses->get($student->id);

                $microExerciseAttempt =
                    $microExerciseAttempts->get(
                        $student->id
                    );

                $quizAttempt =
                    $quizAttempts->get($student->id);

                $missionStatistic =
                    $missionStatistics->get($student->id);

                $badgeStatistic =
                    $badgeStatistics->get($student->id);

                $completedMaterialBlocks = (int) (
                    $materialProgress?->completed_blocks
                    ?? 0
                );

                $materialProgressPercentage =
                    $requiredMaterialBlockCount > 0
                        ? round(
                            (
                                $completedMaterialBlocks
                                / $requiredMaterialBlockCount
                            ) * 100,
                            2
                        )
                        : 0;

                $isPretestCompleted =
                    $pretestAttempt !== null;

                $isMaterialCompleted =
                    $requiredMaterialBlockCount > 0
                    && $completedMaterialBlocks
                        >= $requiredMaterialBlockCount;

                $isMicroExerciseCompleted =
                    $microExerciseAttempt !== null;

                $isQuizCompleted =
                    $quizAttempt !== null;

                /*
                 * Siap Belajar berarti seluruh rangkaian
                 * kegiatan prakelas telah diselesaikan.
                 */
                $isReady =
                    $isPretestCompleted
                    && $isMaterialCompleted
                    && $isMicroExerciseCompleted
                    && $isQuizCompleted;

                /*
                 * Mengambil waktu aktivitas paling baru
                 * dari seluruh kegiatan prakelas.
                 */
                $lastActivityAt = collect([
                    $pretestAttempt?->completed_at,
                    $materialProgress?->last_activity_at,
                    $microExerciseAttempt?->completed_at,
                    $quizAttempt?->completed_at,
                ])
                    ->filter()
                    ->map(
                        fn ($date) =>
                            Carbon::parse($date)
                    )
                    ->sortDesc()
                    ->first();

                $totalReadSeconds = (int) (
                    $materialProgress?->total_read_seconds
                    ?? 0
                );

                return [
                    'student' => $student,

                    'is_ready' => $isReady,

                    'pretest_completed' =>
                        $isPretestCompleted,

                    'material_completed' =>
                        $isMaterialCompleted,

                    'micro_exercise_completed' =>
                        $isMicroExerciseCompleted,

                    'quiz_completed' =>
                        $isQuizCompleted,

                    'pretest_score' =>
                        $pretestAttempt?->score,

                    'micro_exercise_score' =>
                        $microExerciseAttempt?->score,

                    'quiz_score' =>
                        $quizAttempt?->score,

                    'completed_material_blocks' =>
                        $completedMaterialBlocks,

                    'required_material_blocks' =>
                        $requiredMaterialBlockCount,

                    'material_progress_percentage' =>
                        $materialProgressPercentage,

                    'total_read_seconds' =>
                        $totalReadSeconds,

                    'formatted_read_time' =>
                        $this->formatReadTime(
                            $totalReadSeconds
                        ),

                    'total_visits' => (int) (
                        $materialProgress?->total_visits
                        ?? 0
                    ),

                    'last_activity_at' =>
                        $lastActivityAt,

                    'total_xp' => (int) (
                        $missionStatistic?->total_xp
                        ?? 0
                    ),

                    'completed_missions' => (int) (
                        $missionStatistic
                            ?->completed_missions
                        ?? 0
                    ),

                    'earned_badges' => (int) (
                        $badgeStatistic?->earned_badges
                        ?? 0
                    ),
                ];
            }
        );

        $studentCount = $studentMonitoringRows->count();

        $readyStudentCount = $studentMonitoringRows
            ->where('is_ready', true)
            ->count();

        $attentionStudentCount =
    $focusedMission !== null
        ? max(
            0,
            $studentCount - $readyStudentCount
        )
        : 0;

        return view('teacher.monitoring.index', [
            'publishedMissions' => $publishedMissions,
            'focusedMission' => $focusedMission,
            'studentMonitoringRows' =>
                $studentMonitoringRows,
            'studentCount' => $studentCount,
            'readyStudentCount' =>
                $readyStudentCount,
            'attentionStudentCount' =>
                $attentionStudentCount,
            'requiredMaterialBlockCount' =>
                $requiredMaterialBlockCount,
        ]);
    }

    /**
     * Mengubah jumlah detik menjadi durasi baca
     * yang lebih mudah dipahami.
     */
       /**
     * Menampilkan detail aktivitas seorang peserta didik.
     */
    public function show(User $student): View
    {
        /*
         * Route hanya boleh menampilkan akun peserta didik.
         */
        abort_unless(
            $student->role === User::ROLE_STUDENT,
            404
        );

        /*
         * Mengambil misi terbit pertama yang sedang dipantau.
         */
        $focusedMission = Mission::query()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->first([
                'id',
                'title',
                'slug',
                'sequence',
            ]);

        $requiredMaterialBlocks = collect();
        $materialProgresses = collect();

        $pretestAttempt = null;
$microExerciseAttempt = null;
$quizAttempt = null;
$finalChallengeAttempt = null;
$posttestAttempt = null;
$missionCompletion = null;

        if ($focusedMission !== null) {
            /*
             * Mengambil seluruh blok materi aktif dan wajib.
             */
            $requiredMaterialBlocks =
                MaterialBlock::query()
                    ->where('is_active', true)
                    ->where('is_required', true)
                    ->whereHas(
                        'materialSection',
                        function ($query) use (
                            $focusedMission
                        ): void {
                            $query
                                ->where(
                                    'mission_id',
                                    $focusedMission->id
                                )
                                ->where('is_active', true);
                        }
                    )
                    ->with([
                        'materialSection:id,mission_id,title,sequence',
                    ])
                    ->orderBy('material_section_id')
                    ->orderBy('sequence')
                    ->get([
                        'id',
                        'material_section_id',
                        'type',
                        'title',
                        'sequence',
                        'minimum_read_seconds',
                        'is_required',
                    ]);

            /*
             * Mengambil progres setiap blok materi siswa.
             */
            if ($requiredMaterialBlocks->isNotEmpty()) {
                $materialProgresses =
                    MaterialBlockProgress::query()
                        ->where('user_id', $student->id)
                        ->whereIn(
                            'material_block_id',
                            $requiredMaterialBlocks->pluck('id')
                        )
                        ->get()
                        ->keyBy('material_block_id');
            }

            /*
             * Mengambil hasil Pretest terbaru.
             */
            $pretestAttempt =
                PretestAttempt::query()
                    ->where('user_id', $student->id)
                    ->where(
    'mission_id',
    $focusedMission->id
)
->where(
    'status',
    PretestAttempt::STATUS_COMPLETED
)
->latest('completed_at')
->first();

            /*
             * Mengambil hasil Latihan Mikro terbaru.
             */
            $microExerciseAttempt =
                MicroExerciseAttempt::query()
                    ->where('user_id', $student->id)
                    ->where(
                        'mission_id',
                        $focusedMission->id
                    )
                    ->where(
                        'status',
                        MicroExerciseAttempt::STATUS_COMPLETED
                    )
                    ->latest('completed_at')
                    ->first();

            /*
             * Mengambil hasil Kuis Materi terbaru.
             */
            $quizAttempt =
                QuizAttempt::query()
                    ->where('user_id', $student->id)
                    ->where(
                        'mission_id',
                        $focusedMission->id
                    )
                    ->where(
                        'status',
                        QuizAttempt::STATUS_COMPLETED
                    )
                    ->latest('completed_at')
                    ->first();

            /*
             * Mengambil hasil Final Challenge terbaru.
             */
            $finalChallengeAttempt =
                FinalChallengeAttempt::query()
                    ->where('user_id', $student->id)
                    ->where(
                        'mission_id',
                        $focusedMission->id
                    )
                    ->where(
                        'status',
                        FinalChallengeAttempt::STATUS_COMPLETED
                    )
                    ->latest('completed_at')
                    ->first();

                    /*
 * Mengambil hasil Posttest terbaru.
 */
$posttestAttempt = PosttestAttempt::query()
    ->where('user_id', $student->id)
    ->where(
        'mission_id',
        $focusedMission->id
    )
    ->where(
        'status',
        PosttestAttempt::STATUS_COMPLETED
    )
    ->latest('completed_at')
    ->first();

            /*
             * Mengambil catatan penyelesaian misi.
             */
            $missionCompletion =
                MissionCompletion::query()
                    ->where('user_id', $student->id)
                    ->where(
                        'mission_id',
                        $focusedMission->id
                    )
                    ->latest('completed_at')
                    ->first();
        }

        $requiredMaterialBlockCount =
            $requiredMaterialBlocks->count();

        $completedMaterialBlockCount =
            $materialProgresses
                ->where('is_completed', true)
                ->count();

        $materialProgressPercentage =
            $requiredMaterialBlockCount > 0
                ? round(
                    (
                        $completedMaterialBlockCount
                        / $requiredMaterialBlockCount
                    ) * 100,
                    2
                )
                : 0;

        $totalReadSeconds = (int) $materialProgresses
            ->sum('total_read_seconds');

        $totalVisits = (int) $materialProgresses
            ->sum('visit_count');

        /*
         * Menentukan status kesiapan prakelas.
         */
        $isReady =
            $pretestAttempt !== null
            && $requiredMaterialBlockCount > 0
            && $completedMaterialBlockCount
                >= $requiredMaterialBlockCount
            && $microExerciseAttempt !== null
            && $quizAttempt !== null;

            /*
 * Menghitung perubahan nilai dari Pretest ke Posttest.
 */
$learningGain =
    $pretestAttempt !== null
    && $posttestAttempt !== null
        ? round(
            (float) $posttestAttempt->score
            - (float) $pretestAttempt->score,
            2
        )
        : null;

        /*
         * Mengambil aktivitas terakhir dari seluruh tahap.
         */
        $lastActivityAt = collect([
            $pretestAttempt?->completed_at,
            $materialProgresses->max('last_activity_at'),
            $microExerciseAttempt?->completed_at,
            $quizAttempt?->completed_at,
            $finalChallengeAttempt?->completed_at,
            $posttestAttempt?->completed_at,
            $missionCompletion?->completed_at,
        ])
            ->filter()
            ->map(
                fn ($date) => Carbon::parse($date)
            )
            ->sortDesc()
            ->first();

        /*
         * Mengambil seluruh pencapaian siswa.
         */
        $totalXp = (int) MissionCompletion::query()
            ->where('user_id', $student->id)
            ->sum('earned_xp');

        $completedMissionCount =
            MissionCompletion::query()
                ->where('user_id', $student->id)
                ->count();

        $userBadges = UserBadge::query()
            ->where('user_id', $student->id)
            ->with('badge')
            ->latest('awarded_at')
            ->get();

        return view('teacher.monitoring.show', [
            'student' => $student,
            'focusedMission' => $focusedMission,
            'requiredMaterialBlocks' =>
                $requiredMaterialBlocks,
            'materialProgresses' =>
                $materialProgresses,
            'requiredMaterialBlockCount' =>
                $requiredMaterialBlockCount,
            'completedMaterialBlockCount' =>
                $completedMaterialBlockCount,
            'materialProgressPercentage' =>
                $materialProgressPercentage,
            'totalReadSeconds' => $totalReadSeconds,
            'formattedReadTime' =>
                $this->formatReadTime(
                    $totalReadSeconds
                ),
            'totalVisits' => $totalVisits,
            'pretestAttempt' => $pretestAttempt,
            'microExerciseAttempt' =>
                $microExerciseAttempt,
            'quizAttempt' => $quizAttempt,
            'finalChallengeAttempt' =>
                $finalChallengeAttempt,
                'posttestAttempt' =>
    $posttestAttempt,

'learningGain' =>
    $learningGain,
            'missionCompletion' =>
                $missionCompletion,
            'isReady' => $isReady,
            'lastActivityAt' => $lastActivityAt,
            'totalXp' => $totalXp,
            'completedMissionCount' =>
                $completedMissionCount,
            'userBadges' => $userBadges,
        ]);
    }
    private function formatReadTime(
        int $totalSeconds
    ): string {
        if ($totalSeconds <= 0) {
            return '0 menit';
        }

        $hours = intdiv($totalSeconds, 3600);

        $minutes = intdiv(
            $totalSeconds % 3600,
            60
        );

        $seconds = $totalSeconds % 60;

        if ($hours > 0) {
            return "{$hours} jam {$minutes} menit";
        }

        if ($minutes > 0) {
            return "{$minutes} menit {$seconds} detik";
        }

        return "{$seconds} detik";
    }
}
