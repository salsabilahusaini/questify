<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\MicroExerciseQuestion;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MicroExerciseQuestionController extends Controller
{
    /**
     * Menampilkan seluruh soal Latihan Mikro
     * pada sebuah misi.
     */
    public function index(Mission $mission): View
    {
        $questions = MicroExerciseQuestion::query()
            ->whereHas(
                'materialBlock.materialSection',
                function ($query) use ($mission): void {
                    $query->where(
                        'mission_id',
                        $mission->id
                    );
                }
            )
            ->with([
                'materialBlock:id,material_section_id,title,type,sequence',
                'materialBlock.materialSection:id,mission_id,title,sequence',
                'options' => function ($query): void {
                    $query->orderBy('sequence');
                },
            ])
            ->get()
            ->sortBy(
                function (
                    MicroExerciseQuestion $question
                ): string {
                    $sectionSequence = (int) (
                        $question
                            ->materialBlock
                            ?->materialSection
                            ?->sequence ?? 0
                    );

                    $blockSequence = (int) (
                        $question
                            ->materialBlock
                            ?->sequence ?? 0
                    );

                    return sprintf(
                        '%06d-%06d-%06d',
                        $sectionSequence,
                        $blockSequence,
                        $question->sequence
                    );
                }
            )
            ->values();

        $totalQuestionCount = $questions->count();

        $activeQuestionCount = $questions
            ->where('is_active', true)
            ->count();

        $completeQuestionCount = $questions
            ->filter(
                function (
                    MicroExerciseQuestion $question
                ): bool {
                    return $question->options->count() >= 2
                        && $question->options
                            ->where('is_correct', true)
                            ->count() === 1;
                }
            )
            ->count();

        $totalPointCount = (int) $questions
            ->sum('points');

        $materialBlockCount = $questions
            ->pluck('material_block_id')
            ->unique()
            ->count();

        return view(
            'teacher.missions.micro-exercises.index',
            [
                'mission' => $mission,
                'questions' => $questions,
                'totalQuestionCount' =>
                    $totalQuestionCount,
                'activeQuestionCount' =>
                    $activeQuestionCount,
                'completeQuestionCount' =>
                    $completeQuestionCount,
                'totalPointCount' =>
                    $totalPointCount,
                'materialBlockCount' =>
                    $materialBlockCount,
            ]
        );
    }
    /**
 * Menampilkan formulir tambah soal
 * Latihan Mikro.
 */
public function create(Mission $mission): View
{
    $materialBlocks = MaterialBlock::query()
        ->whereHas(
            'materialSection',
            function ($query) use ($mission): void {
                $query->where(
                    'mission_id',
                    $mission->id
                );
            }
        )
        ->with([
            'materialSection:id,mission_id,title,sequence',
            'microExerciseQuestions:id,material_block_id,sequence',
        ])
        ->get()
        ->sortBy(
            function (MaterialBlock $block): string {
                $sectionSequence = (int) (
                    $block
                        ->materialSection
                        ?->sequence ?? 0
                );

                return sprintf(
                    '%06d-%06d',
                    $sectionSequence,
                    $block->sequence
                );
            }
        )
        ->values();

    $nextSequenceByBlock = $materialBlocks
        ->mapWithKeys(
            function (MaterialBlock $block): array {
                $highestSequence = (int)
                    $block
                        ->microExerciseQuestions
                        ->max('sequence');

                return [
                    $block->id =>
                        $highestSequence + 1,
                ];
            }
        );

    return view(
        'teacher.missions.micro-exercises.create',
        [
            'mission' => $mission,
            'materialBlocks' => $materialBlocks,
            'nextSequenceByBlock' =>
                $nextSequenceByBlock,
        ]
    );
}
/**
 * Menyimpan soal Latihan Mikro baru beserta
 * empat pilihan jawabannya.
 */
public function store(
    Request $request,
    Mission $mission
): RedirectResponse {
    $validated = $request->validate([
        'material_block_id' => [
            'required',
            'integer',
            'exists:material_blocks,id',
        ],
        'question_text' => [
            'required',
            'string',
            'max:5000',
        ],
        'sequence' => [
            'required',
            'integer',
            'min:1',
            Rule::unique(
                'micro_exercise_questions',
                'sequence'
            )->where(
                fn ($query) => $query->where(
                    'material_block_id',
                    $request->input(
                        'material_block_id'
                    )
                )
            ),
        ],
        'points' => [
            'required',
            'integer',
            'min:0',
            'max:10000',
        ],
        'correct_feedback' => [
            'nullable',
            'string',
            'max:2000',
        ],
        'incorrect_feedback' => [
            'nullable',
            'string',
            'max:2000',
        ],
        'options' => [
            'required',
            'array',
            'size:4',
        ],
        'options.*.option_text' => [
            'required',
            'string',
            'max:1000',
        ],
        'options.*.sequence' => [
            'required',
            'integer',
            'min:1',
            'distinct',
        ],
        'correct_option_index' => [
            'required',
            'integer',
            'in:0,1,2,3',
        ],
    ]);

    /*
     * Memastikan blok materi benar-benar
     * berasal dari misi yang sedang dikelola.
     */
    $materialBlock = MaterialBlock::query()
        ->whereKey(
            $validated['material_block_id']
        )
        ->whereHas(
            'materialSection',
            function ($query) use ($mission): void {
                $query->where(
                    'mission_id',
                    $mission->id
                );
            }
        )
        ->first();

    if ($materialBlock === null) {
        throw ValidationException::withMessages([
            'material_block_id' =>
                'Blok materi tidak valid untuk misi ini.',
        ]);
    }

    DB::transaction(function () use (
        $request,
        $materialBlock,
        $validated
    ): void {
        $question = MicroExerciseQuestion::create([
            'material_block_id' =>
                $materialBlock->id,
            'type' =>
                MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text' =>
                $validated['question_text'],
            'sequence' =>
                $validated['sequence'],
            'points' =>
                $validated['points'],
            'correct_feedback' =>
                $validated['correct_feedback']
                ?? null,
            'incorrect_feedback' =>
                $validated['incorrect_feedback']
                ?? null,
            'is_active' =>
                $request->boolean('is_active'),
        ]);

        $correctOptionIndex = (int)
            $validated['correct_option_index'];

        foreach (
            $validated['options']
            as $index => $optionData
        ) {
            $question->options()->create([
                'option_text' =>
                    $optionData['option_text'],
                'sequence' =>
                    $optionData['sequence'],
                'is_correct' =>
                    (int) $index
                    === $correctOptionIndex,
            ]);
        }
    });

    return redirect()
        ->route(
            'teacher.missions.micro-exercises.index',
            $mission
        )
        ->with(
            'success',
            'Soal Latihan Mikro berhasil ditambahkan.'
        );
}
    /**
     * Menampilkan formulir edit soal
     * Latihan Mikro.
     */
    public function edit(
        Mission $mission,
        MicroExerciseQuestion $question
    ): View {
        $this->ensureQuestionBelongsToMission(
            $mission,
            $question
        );

        $question->load([
            'materialBlock.materialSection',
            'options' => function ($query): void {
                $query->orderBy('sequence');
            },
        ]);

        return view(
            'teacher.missions.micro-exercises.edit',
            [
                'mission' => $mission,
                'question' => $question,
                'block' =>
                    $question->materialBlock,
                'section' =>
                    $question
                        ->materialBlock
                        ->materialSection,
            ]
        );
    }

    /**
     * Menyimpan perubahan soal, poin,
     * umpan balik, dan pilihan jawaban.
     */
    public function update(
        Request $request,
        Mission $mission,
        MicroExerciseQuestion $question
    ): RedirectResponse {
        $this->ensureQuestionBelongsToMission(
            $mission,
            $question
        );

        $question->load('options');

        $validated = $request->validate([
            'question_text' => [
                'required',
                'string',
                'max:5000',
            ],
            'sequence' => [
                'required',
                'integer',
                'min:1',
            ],
            'points' => [
                'required',
                'integer',
                'min:0',
                'max:10000',
            ],
            'correct_feedback' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'incorrect_feedback' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'options' => [
                'required',
                'array',
                'min:2',
            ],
            'options.*.option_text' => [
                'required',
                'string',
                'max:1000',
            ],
            'options.*.sequence' => [
                'required',
                'integer',
                'min:1',
            ],
            'correct_option_id' => [
                'required',
                'integer',
                Rule::exists(
                    'micro_exercise_options',
                    'id'
                )->where(
                    function ($query) use (
                        $question
                    ): void {
                        $query->where(
                            'micro_exercise_question_id',
                            $question->id
                        );
                    }
                ),
            ],
        ]);

        /*
         * Memastikan seluruh pilihan yang sudah ada
         * tetap dikirim melalui formulir.
         */
        $existingOptionIds = $question
            ->options
            ->pluck('id')
            ->map(
                fn ($id): int => (int) $id
            )
            ->sort()
            ->values();

        $submittedOptionIds = collect(
            array_keys($validated['options'])
        )
            ->map(
                fn ($id): int => (int) $id
            )
            ->sort()
            ->values();

        if (
            $existingOptionIds->all()
            !== $submittedOptionIds->all()
        ) {
            throw ValidationException::withMessages([
                'options' =>
                    'Data pilihan jawaban tidak lengkap.',
            ]);
        }

        DB::transaction(function () use (
            $request,
            $question,
            $validated
        ): void {
            $question->update([
                'type' =>
                    MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
                'question_text' =>
                    $validated['question_text'],
                'sequence' =>
                    $validated['sequence'],
                'points' =>
                    $validated['points'],
                'correct_feedback' =>
                    $validated['correct_feedback']
                    ?? null,
                'incorrect_feedback' =>
                    $validated['incorrect_feedback']
                    ?? null,
                'is_active' =>
                    $request->boolean('is_active'),
            ]);

            $correctOptionId = (int)
                $validated['correct_option_id'];

            foreach (
                $question->options
                as $option
            ) {
                $optionData =
                    $validated['options'][$option->id];

                $option->update([
                    'option_text' =>
                        $optionData['option_text'],
                    'sequence' =>
                        $optionData['sequence'],
                    'is_correct' =>
                        (int) $option->id
                        === $correctOptionId,
                ]);
            }
        });

        return redirect()
            ->route(
                'teacher.missions.micro-exercises.index',
                $mission
            )
            ->with(
                'success',
                'Soal Latihan Mikro berhasil diperbarui.'
            );
    }
    /**
 * Menghapus soal Latihan Mikro yang belum pernah
 * digunakan dalam jawaban peserta didik.
 */
public function destroy(
    Mission $mission,
    MicroExerciseQuestion $question
): RedirectResponse {
    $this->ensureQuestionBelongsToMission(
        $mission,
        $question
    );

    $hasStudentAnswers = DB::table(
        'micro_exercise_answers'
    )
        ->where(
            'micro_exercise_question_id',
            $question->id
        )
        ->exists();

    if ($hasStudentAnswers) {
        return redirect()
            ->route(
                'teacher.missions.micro-exercises.index',
                $mission
            )
            ->with(
                'error',
                'Soal Latihan Mikro tidak dapat dihapus karena sudah memiliki jawaban peserta didik. Nonaktifkan soal melalui menu Edit Soal.'
            );
    }

    $question->delete();

    return redirect()
        ->route(
            'teacher.missions.micro-exercises.index',
            $mission
        )
        ->with(
            'success',
            'Soal Latihan Mikro berhasil dihapus.'
        );
}
    /**
     * Memastikan soal Latihan Mikro berasal
     * dari misi yang sedang dikelola.
     */
    private function ensureQuestionBelongsToMission(
        Mission $mission,
        MicroExerciseQuestion $question
    ): void {
        $question->loadMissing(
            'materialBlock.materialSection'
        );

        $questionMissionId = (int) (
            $question
                ->materialBlock
                ?->materialSection
                ?->mission_id ?? 0
        );

        abort_unless(
            $questionMissionId
                === (int) $mission->id,
            404
        );
    }
}
