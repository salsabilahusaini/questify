<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\PosttestQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosttestQuestionController extends Controller
{
    /**
     * Menampilkan daftar soal Posttest pada sebuah misi.
     */
    public function index(Mission $mission): View
    {
        $questions = PosttestQuestion::query()
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

        $totalOptionCount = $questions->sum(
            fn (PosttestQuestion $question): int =>
                $question->options->count()
        );

        /*
         * Soal dianggap lengkap jika memiliki minimal
         * dua pilihan dan tepat satu jawaban benar.
         */
        $completeQuestionCount = $questions
            ->filter(
                function (
                    PosttestQuestion $question
                ): bool {
                    return $question->options->count() >= 2
                        && $question->options
                            ->where('is_correct', true)
                            ->count() === 1;
                }
            )
            ->count();

        return view(
            'teacher.missions.posttest.index',
            [
                'mission' => $mission,
                'questions' => $questions,
                'totalQuestionCount' =>
                    $totalQuestionCount,
                'activeQuestionCount' =>
                    $activeQuestionCount,
                'completeQuestionCount' =>
                    $completeQuestionCount,
                'totalOptionCount' =>
                    $totalOptionCount,
            ]
        );
    }
/**
 * Menampilkan formulir tambah soal Posttest.
 */
public function create(Mission $mission): View
{
    $nextSequence = (int) PosttestQuestion::query()
        ->where('mission_id', $mission->id)
        ->max('sequence') + 1;

    return view(
        'teacher.missions.posttest.create',
        [
            'mission' => $mission,
            'nextSequence' => $nextSequence,
        ]
    );
}
/**
 * Menyimpan soal Posttest baru beserta
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
        $question = PosttestQuestion::create([
            'mission_id' => $mission->id,
            'question_text' =>
                $validated['question_text'],
            'sequence' => $validated['sequence'],
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
            'teacher.missions.posttest.index',
            $mission
        )
        ->with(
            'success',
            'Soal Posttest berhasil ditambahkan.'
        );
}
    /**
     * Menampilkan formulir edit soal Posttest.
     */
    public function edit(
        Mission $mission,
        PosttestQuestion $question
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
            'teacher.missions.posttest.edit',
            [
                'mission' => $mission,
                'question' => $question,
            ]
        );
    }

    /**
     * Menyimpan perubahan soal dan pilihan Posttest.
     */
    public function update(
        Request $request,
        Mission $mission,
        PosttestQuestion $question
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
                    'posttest_options',
                    'id'
                )->where(
                    function ($query) use (
                        $question
                    ): void {
                        $query->where(
                            'posttest_question_id',
                            $question->id
                        );
                    }
                ),
            ],
        ]);

        /*
         * Memastikan seluruh pilihan milik soal tetap
         * dikirim oleh formulir.
         */
        $existingOptionIds = $question->options
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
                'question_text' =>
                    $validated['question_text'],
                'sequence' =>
                    $validated['sequence'],
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
                'teacher.missions.posttest.index',
                $mission
            )
            ->with(
                'success',
                'Soal Posttest berhasil diperbarui.'
            );
    }

    /**
     * Memastikan soal Posttest berasal dari misi
     * yang sedang dikelola.
     */
    private function ensureQuestionBelongsToMission(
        Mission $mission,
        PosttestQuestion $question
    ): void {
        abort_unless(
            (int) $question->mission_id
                === (int) $mission->id,
            404
        );
    }
}
