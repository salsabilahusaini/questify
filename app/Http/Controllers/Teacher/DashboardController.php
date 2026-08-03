<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\PretestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard dan ringkasan monitoring guru.
     */
    public function __invoke(): View
    {
        /*
         * Mengambil seluruh ID peserta didik yang terdaftar.
         */
        $studentIds = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->pluck('id');

        $studentCount = $studentIds->count();

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

        $activeModuleCount = $publishedMissions->count();

        /*
         * Untuk tahap awal, dashboard memantau misi terbit
         * pertama berdasarkan urutan sequence.
         */
        $focusedMission = $publishedMissions->first();

        /*
         * Nilai awal apabila belum ada misi yang dipublikasikan.
         */
        $pretestCompletedCount = 0;
        $materialCompletedCount = 0;
        $microExerciseCompletedCount = 0;
        $quizCompletedCount = 0;
        $readyStudentCount = 0;
$attentionStudentCount = 0;
$requiredMaterialBlockCount = 0;;

        if ($focusedMission !== null && $studentCount > 0) {
            /*
             * Siswa yang telah menyelesaikan Pretest
             * pada misi yang sedang dipantau.
             */
            $pretestCompletedStudentIds =
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
->distinct()
                    ->pluck('user_id');

            /*
             * Mengambil seluruh blok materi aktif dan wajib
             * pada bagian materi yang juga masih aktif.
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
             * Siswa dianggap menyelesaikan Materi Prakelas
             * apabila seluruh blok aktif dan wajib telah selesai.
             */
            if ($requiredMaterialBlockCount > 0) {
                $materialCompletedStudentIds =
                    MaterialBlockProgress::query()
                        ->whereIn('user_id', $studentIds)
                        ->whereIn(
                            'material_block_id',
                            $requiredMaterialBlockIds
                        )
                        ->where('is_completed', true)
                        ->select('user_id')
                        ->groupBy('user_id')
                        ->havingRaw(
                            'COUNT(DISTINCT material_block_id) >= ?',
                            [$requiredMaterialBlockCount]
                        )
                        ->pluck('user_id');
            } else {
                $materialCompletedStudentIds = collect();
            }

            /*
             * Siswa yang telah menyelesaikan Latihan Mikro.
             */
            $microExerciseCompletedStudentIds =
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
                    ->distinct()
                    ->pluck('user_id');

            /*
             * Siswa yang telah menyelesaikan Kuis Materi.
             */
            $quizCompletedStudentIds =
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
                    ->distinct()
                    ->pluck('user_id');

            $pretestCompletedCount =
                $pretestCompletedStudentIds->count();

            $materialCompletedCount =
                $materialCompletedStudentIds->count();

            $microExerciseCompletedCount =
                $microExerciseCompletedStudentIds->count();

            $quizCompletedCount =
                $quizCompletedStudentIds->count();

            /*
             * Siswa berstatus Siap Belajar hanya apabila
             * seluruh kegiatan prakelas telah diselesaikan.
             */
            $readyStudentIds = $studentIds
                ->intersect($pretestCompletedStudentIds)
                ->intersect($materialCompletedStudentIds)
                ->intersect(
                    $microExerciseCompletedStudentIds
                )
                ->intersect($quizCompletedStudentIds)
                ->values();

            $readyStudentCount = $readyStudentIds->count();

            /*
             * Peserta didik yang belum memenuhi seluruh
             * kegiatan prakelas masuk kategori perhatian.
             */
            $attentionStudentCount = max(
                0,
                $studentCount - $readyStudentCount
            );
        }

        return view('teacher.dashboard', [
            'studentCount' => $studentCount,
            'activeModuleCount' => $activeModuleCount,
            'readyStudentCount' => $readyStudentCount,
            'attentionStudentCount' =>
                $attentionStudentCount,
            'focusedMission' => $focusedMission,
            'pretestCompletedCount' =>
                $pretestCompletedCount,
            'materialCompletedCount' =>
                $materialCompletedCount,
            'microExerciseCompletedCount' =>
                $microExerciseCompletedCount,
            'quizCompletedCount' =>
                $quizCompletedCount,
            'requiredMaterialBlockCount' =>
                $requiredMaterialBlockCount,
        ]);
    }
}
