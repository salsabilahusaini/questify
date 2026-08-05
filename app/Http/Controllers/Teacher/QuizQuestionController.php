<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuizQuestionController extends Controller
{
    /**
     * Menampilkan seluruh soal Kuis Materi
     * pada sebuah misi.
     */
    public function index(Mission $mission): View
    {
        $questions = QuizQuestion::query()
            ->where('mission_id', $mission->id)
            ->with([
                'options' => function ($query): void {
                    $query->orderBy('sequence');
                },
            ])
            ->orderBy('sequence')
            ->get();

        $totalQuestionCount = $questions->count();

        $activeQuestionCount = $questions
            ->where('is_active', true)
            ->count();

        /*
         * Soal dianggap lengkap jika memiliki minimal
         * dua pilihan dan tepat satu jawaban benar.
         */
        $completeQuestionCount = $questions
            ->filter(
                function (
                    QuizQuestion $question
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

        $totalOptionCount = $questions->sum(
            fn (QuizQuestion $question): int =>
                $question->options->count()
        );

        return view(
            'teacher.missions.quizzes.index',
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
                'totalOptionCount' =>
                    $totalOptionCount,
            ]
        );
    }
/**
 * Menampilkan formulir tambah soal Kuis Materi.
 */
public function create(Mission $mission): View
{
    $nextSequence = (int) QuizQuestion::query()
        ->where('mission_id', $mission->id)
        ->max('sequence') + 1;

    return view(
        'teacher.missions.quizzes.create',
        [
            'mission' => $mission,
            'nextSequence' => $nextSequence,
        ]
    );
}

/**
 * Menyimpan soal Kuis Materi baru beserta
 * empat pilihan jawabannya.
 */
public function store(
    Request $request,
    Mission $mission
): RedirectResponse {
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
            Rule::unique(
                'quiz_questions',
                'sequence'
            )->where(
                fn ($query) => $query->where(
                    'mission_id',
                    $mission->id
                )
            ),
        ],
        'points' => [
            'required',
            'integer',
            'min:0',
            'max:10000',
        ],
        'explanation' => [
            'nullable',
            'string',
            'max:3000',
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

    DB::transaction(function () use (
        $request,
        $mission,
        $validated
    ): void {
        $question = QuizQuestion::create([
            'mission_id' => $mission->id,
            'type' =>
                QuizQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text' =>
                $validated['question_text'],
            'sequence' =>
                $validated['sequence'],
            'points' =>
                $validated['points'],
            'explanation' =>
                $validated['explanation'] ?? null,
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
            'teacher.missions.quizzes.index',
            $mission
        )
        ->with(
            'success',
            'Soal Kuis Materi berhasil ditambahkan.'
        );
}
    /**
     * Menampilkan formulir edit soal Kuis Materi.
     */
    public function edit(
        Mission $mission,
        QuizQuestion $question
    ): View {
        $this->ensureQuestionBelongsToMission(
            $mission,
            $question
        );

        $question->load([
            'options' => function ($query): void {
                $query->orderBy('sequence');
            },
        ]);

        return view(
            'teacher.missions.quizzes.edit',
            [
                'mission' => $mission,
                'question' => $question,
            ]
        );
    }

    /**
     * Menyimpan perubahan soal dan pilihan
     * jawaban Kuis Materi.
     */
    public function update(
        Request $request,
        Mission $mission,
        QuizQuestion $question
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
            'explanation' => [
                'nullable',
                'string',
                'max:3000',
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
                    'quiz_options',
                    'id'
                )->where(
                    function ($query) use (
                        $question
                    ): void {
                        $query->where(
                            'quiz_question_id',
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
                    QuizQuestion::TYPE_MULTIPLE_CHOICE,
                'question_text' =>
                    $validated['question_text'],
                'sequence' =>
                    $validated['sequence'],
                'points' =>
                    $validated['points'],
                'explanation' =>
                    $validated['explanation'] ?? null,
                'is_active' =>
                    $request->boolean('is_active'),
            ]);

            $correctOptionId = (int)
                $validated['correct_option_id'];

            foreach (
                $question->options as $option
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
                'teacher.missions.quizzes.index',
                $mission
            )
            ->with(
                'success',
                'Soal Kuis Materi berhasil diperbarui.'
            );
    }
    /**
 * Menghapus soal Kuis Materi yang belum pernah
 * digunakan dalam jawaban peserta didik.
 */
public function destroy(
    Mission $mission,
    QuizQuestion $question
): RedirectResponse {
    $this->ensureQuestionBelongsToMission(
        $mission,
        $question
    );

    $hasStudentAnswers = DB::table(
        'quiz_answers'
    )
        ->where(
            'quiz_question_id',
            $question->id
        )
        ->exists();

    if ($hasStudentAnswers) {
        return redirect()
            ->route(
                'teacher.missions.quizzes.index',
                $mission
            )
            ->with(
                'error',
                'Soal Kuis Materi tidak dapat dihapus karena sudah memiliki jawaban peserta didik. Nonaktifkan soal melalui menu Edit Soal.'
            );
    }

    $question->delete();

    return redirect()
        ->route(
            'teacher.missions.quizzes.index',
            $mission
        )
        ->with(
            'success',
            'Soal Kuis Materi berhasil dihapus.'
        );
}

    /**
     * Memastikan soal Kuis Materi berasal
     * dari misi yang sedang dikelola.
     */
    private function ensureQuestionBelongsToMission(
        Mission $mission,
        QuizQuestion $question
    ): void {
        abort_unless(
            (int) $question->mission_id
                === (int) $mission->id,
            404
        );
    }
}
