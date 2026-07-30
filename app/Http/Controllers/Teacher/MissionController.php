<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MaterialBlock;
use App\Models\MicroExerciseQuestion;
use App\Models\Mission;
use Illuminate\View\View;

class MissionController extends Controller
{
    /**
     * Menampilkan daftar misi pembelajaran untuk guru.
     */
    public function index(): View
    {
        /*
         * Mengambil seluruh misi beserta jumlah bagian,
         * soal, penyelesaian, dan Final Challenge.
         */
        $missions = Mission::query()
            ->with([
                'finalChallenge:id,mission_id',
            ])
            ->withCount([
                'pretestQuestions',
                'materialSections',
                'quizQuestions',
                'completions',
            ])
            ->orderBy('sequence')
            ->get([
                'id',
                'title',
                'slug',
                'description',
                'sequence',
                'xp_reward',
                'is_published',
                'created_at',
                'updated_at',
            ]);

        /*
         * Menambahkan statistik materi dan Latihan Mikro
         * untuk setiap misi.
         */
        $missionRows = $missions->map(
            function (Mission $mission): array {
                $materialBlocks = MaterialBlock::query()
                    ->whereHas(
                        'materialSection',
                        function ($query) use (
                            $mission
                        ): void {
                            $query->where(
                                'mission_id',
                                $mission->id
                            );
                        }
                    )
                    ->get([
                        'id',
                        'is_active',
                        'is_required',
                    ]);

                $materialBlockIds =
                    $materialBlocks->pluck('id');

                $microExerciseQuestionCount =
                    $materialBlockIds->isNotEmpty()
                        ? MicroExerciseQuestion::query()
                            ->whereIn(
                                'material_block_id',
                                $materialBlockIds
                            )
                            ->count()
                        : 0;

                $activeMaterialBlockCount =
                    $materialBlocks
                        ->where('is_active', true)
                        ->count();

                $requiredMaterialBlockCount =
                    $materialBlocks
                        ->where('is_active', true)
                        ->where('is_required', true)
                        ->count();

                /*
                 * Menghitung tahap pembelajaran yang sudah
                 * memiliki konten pada misi.
                 */
                $configuredStageCount = collect([
                    (int) $mission
                        ->pretest_questions_count > 0,

                    $activeMaterialBlockCount > 0,

                    $microExerciseQuestionCount > 0,

                    (int) $mission
                        ->quiz_questions_count > 0,

                    $mission->finalChallenge !== null,
                ])
                    ->filter()
                    ->count();

                return [
                    'mission' => $mission,

                    'pretest_question_count' => (int) (
                        $mission
                            ->pretest_questions_count
                        ?? 0
                    ),

                    'material_section_count' => (int) (
                        $mission
                            ->material_sections_count
                        ?? 0
                    ),

                    'material_block_count' =>
                        $materialBlocks->count(),

                    'active_material_block_count' =>
                        $activeMaterialBlockCount,

                    'required_material_block_count' =>
                        $requiredMaterialBlockCount,

                    'micro_exercise_question_count' =>
                        $microExerciseQuestionCount,

                    'quiz_question_count' => (int) (
                        $mission
                            ->quiz_questions_count
                        ?? 0
                    ),

                    'has_final_challenge' =>
                        $mission->finalChallenge !== null,

                    'completion_count' => (int) (
                        $mission->completions_count
                        ?? 0
                    ),

                    'configured_stage_count' =>
                        $configuredStageCount,
                ];
            }
        );

        /*
         * Ringkasan seluruh misi.
         */
        $totalMissionCount = $missionRows->count();

        $publishedMissionCount = $missionRows
            ->filter(
                fn (array $row): bool =>
                    (bool) $row['mission']
                        ->is_published
            )
            ->count();

        $draftMissionCount = max(
            0,
            $totalMissionCount
                - $publishedMissionCount
        );

        $totalCompletionCount = (int) $missionRows
            ->sum('completion_count');

        return view('teacher.missions.index', [
            'missionRows' => $missionRows,
            'totalMissionCount' =>
                $totalMissionCount,
            'publishedMissionCount' =>
                $publishedMissionCount,
            'draftMissionCount' =>
                $draftMissionCount,
            'totalCompletionCount' =>
                $totalCompletionCount,
        ]);
    }
        /**
     * Menampilkan rincian sebuah misi pembelajaran.
     */
    public function show(Mission $mission): View
    {
        /*
         * Mengambil bagian materi, blok materi,
         * jumlah Latihan Mikro, dan Final Challenge.
         */
        $mission->load([
            'materialSections' => function ($query): void {
                $query
                    ->orderBy('sequence')
                    ->with([
                        'blocks' => function ($query): void {
                            $query
                                ->orderBy('sequence')
                                ->withCount(
                                    'microExerciseQuestions'
                                );
                        },
                    ]);
            },
            'finalChallenge:id,mission_id',
        ]);

        /*
         * Mengambil jumlah soal dan penyelesaian misi.
         */
        $mission->loadCount([
            'pretestQuestions',
            'quizQuestions',
            'completions',
        ]);

        /*
         * Menggabungkan seluruh blok dari setiap
         * bagian materi.
         */
        $materialBlocks = $mission
            ->materialSections
            ->flatMap(
                fn ($section) => $section->blocks
            );

        $materialSectionCount =
            $mission->materialSections->count();

        $materialBlockCount =
            $materialBlocks->count();

        $activeMaterialBlockCount =
            $materialBlocks
                ->where('is_active', true)
                ->count();

        $requiredMaterialBlockCount =
            $materialBlocks
                ->where('is_active', true)
                ->where('is_required', true)
                ->count();

        $microExerciseQuestionCount = (int)
            $materialBlocks->sum(
                'micro_exercise_questions_count'
            );

        /*
         * Menentukan kesiapan kelima tahap misi.
         */
        $stages = collect([
            [
                'label' => 'Pretest',
                'configured' =>
                    $mission->pretest_questions_count > 0,
                'detail' =>
                    $mission->pretest_questions_count
                    . ' soal',
            ],
            [
                'label' => 'Materi Prakelas',
                'configured' =>
                    $activeMaterialBlockCount > 0,
                'detail' =>
                    $activeMaterialBlockCount
                    . ' blok aktif',
            ],
            [
                'label' => 'Latihan Mikro',
                'configured' =>
                    $microExerciseQuestionCount > 0,
                'detail' =>
                    $microExerciseQuestionCount
                    . ' soal',
            ],
            [
                'label' => 'Kuis Materi',
                'configured' =>
                    $mission->quiz_questions_count > 0,
                'detail' =>
                    $mission->quiz_questions_count
                    . ' soal',
            ],
            [
                'label' => 'Final Challenge',
                'configured' =>
                    $mission->finalChallenge !== null,
                'detail' =>
                    $mission->finalChallenge !== null
                        ? 'Tersedia'
                        : 'Belum tersedia',
            ],
        ]);

        $configuredStageCount = $stages
            ->where('configured', true)
            ->count();

        $configuredStagePercentage = (int) round(
            ($configuredStageCount / 5) * 100
        );

        return view('teacher.missions.show', [
            'mission' => $mission,
            'stages' => $stages,
            'materialBlocks' => $materialBlocks,
            'materialSectionCount' =>
                $materialSectionCount,
            'materialBlockCount' =>
                $materialBlockCount,
            'activeMaterialBlockCount' =>
                $activeMaterialBlockCount,
            'requiredMaterialBlockCount' =>
                $requiredMaterialBlockCount,
            'microExerciseQuestionCount' =>
                $microExerciseQuestionCount,
            'configuredStageCount' =>
                $configuredStageCount,
            'configuredStagePercentage' =>
                $configuredStagePercentage,
        ]);
    }
        /**
     * Menampilkan formulir edit informasi dasar misi.
     */
    public function edit(Mission $mission): View
    {
        return view('teacher.missions.edit', [
            'mission' => $mission,
        ]);
    }

    /**
     * Menyimpan perubahan informasi dasar misi.
     */
    public function update(
        Request $request,
        Mission $mission
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'sequence' => [
                'required',
                'integer',
                'min:1',
            ],
            'xp_reward' => [
                'required',
                'integer',
                'min:0',
                'max:10000',
            ],
        ]);

        /*
         * Checkbox yang tidak dicentang tidak dikirim
         * oleh browser, sehingga dibaca menggunakan
         * method boolean().
         */
        $validated['is_published'] =
            $request->boolean('is_published');

        $mission->update($validated);

        return redirect()
            ->route(
                'teacher.missions.show',
                $mission
            )
            ->with(
                'success',
                'Informasi misi berhasil diperbarui.'
            );
    }
}
