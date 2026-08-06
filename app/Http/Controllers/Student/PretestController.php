<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\PretestAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PretestController extends Controller
{
    /**
     * Menampilkan halaman awal Pretest.
     */
    public function show(Request $request, Mission $mission): View
    {
        abort_unless($mission->is_published, 404);

        $questionCount = $mission->pretestQuestions()
            ->where('is_active', true)
            ->count();

        abort_if(
            $questionCount === 0,
            404,
            'Soal Pretest belum tersedia.'
        );

        $attempt = $mission->pretestAttempts()
            ->where('user_id', $request->user()->id)
            ->first();

        return view('student.pretest.show', [
            'mission' => $mission,
            'questionCount' => $questionCount,
            'attempt' => $attempt,
        ]);
    }
    /**
 * Memulai pengerjaan Pretest siswa.
 */
public function start(Request $request, Mission $mission): RedirectResponse
{
    abort_unless($mission->is_published, 404);

    $questionIds = $mission->pretestQuestions()
    ->where('is_active', true)
    ->pluck('id');

abort_if(
    $questionIds->isEmpty(),
    404,
    'Soal Pretest belum tersedia.'
);

$attempt = $mission->pretestAttempts()->firstOrCreate(
    [
        'user_id' => $request->user()->id,
    ],
    [
        'status' => PretestAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
        'total_questions' => $questionIds->count(),
        'correct_answers' => 0,
        'question_order' => $questionIds
            ->shuffle()
            ->values()
            ->all(),
    ]
);

/*
 * Attempt lama yang masih berlangsung mungkin belum
 * memiliki urutan soal karena dibuat sebelum fitur acak.
 */
if (
    $attempt->status === PretestAttempt::STATUS_IN_PROGRESS
    && empty($attempt->question_order)
) {
    $attempt->update([
        'total_questions' => $questionIds->count(),
        'question_order' => $questionIds
            ->shuffle()
            ->values()
            ->all(),
    ]);
}

    return redirect()->route(
    'student.missions.pretest.show',
    ['mission' => $mission->slug]
);
}
/**
 * Menampilkan soal Pretest yang sedang dikerjakan.
 */
public function questions(
    Request $request,
    Mission $mission
): View|RedirectResponse {
    abort_unless($mission->is_published, 404);

    $attempt = $mission->pretestAttempts()
        ->where('user_id', $request->user()->id)
        ->first();

    if (! $attempt) {
        return redirect()
            ->route(
                'student.missions.pretest.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'error',
                'Mulai Pretest terlebih dahulu.'
            );
    }
 if (
    $attempt->status
    === PretestAttempt::STATUS_COMPLETED
) {

        return redirect()->route(
            'student.missions.pretest.show',
            ['mission' => $mission->slug]
        );
    }

    $questionOrder = collect($attempt->question_order)
    ->map(
        fn ($questionId): int => (int) $questionId
    )
    ->values();

/*
 * Menangani attempt lama yang dibuat sebelum
 * kolom question_order tersedia.
 */
if ($questionOrder->isEmpty()) {
    $questionOrder = $mission->pretestQuestions()
        ->where('is_active', true)
        ->pluck('id')
        ->shuffle()
        ->values();

    abort_if(
        $questionOrder->isEmpty(),
        404,
        'Soal Pretest belum tersedia.'
    );

    $attempt->update([
        'total_questions' => $questionOrder->count(),
        'question_order' => $questionOrder->all(),
    ]);
}

$questions = $mission->pretestQuestions()
    ->whereIn('id', $questionOrder->all())
    ->select([
        'id',
        'mission_id',
        'question_text',
        'sequence',
    ])
    ->with([
        'options' => function ($query): void {
            $query
                ->select([
                    'id',
                    'pretest_question_id',
                    'option_text',
                    'sequence',
                ])
                ->orderBy('sequence');
        },
    ])
    ->get();

$orderPositions = $questionOrder->flip();

$questions = $questions
    ->sortBy(
        fn ($question): int => (int) $orderPositions->get(
            $question->id,
            PHP_INT_MAX
        )
    )
    ->values();

    abort_if(
        $questions->isEmpty(),
        404,
        'Soal Pretest belum tersedia.'
    );

    return view('student.pretest.questions', [
        'mission' => $mission,
        'attempt' => $attempt,
        'questions' => $questions,
    ]);
}
/**
 * Menyimpan jawaban dan menyelesaikan Pretest siswa.
 */
public function submit(
    Request $request,
    Mission $mission
): RedirectResponse {
    abort_unless($mission->is_published, 404);

    $attempt = $mission->pretestAttempts()
        ->where('user_id', $request->user()->id)
        ->first();

    if (! $attempt) {
        return redirect()
            ->route(
                'student.missions.pretest.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'error',
                'Mulai Pretest terlebih dahulu.'
            );
    }

    if (
    $attempt->status
    === PretestAttempt::STATUS_COMPLETED
) {
        return redirect()->route(
            'student.missions.pretest.show',
            ['mission' => $mission->slug]
        );
    }

    $validated = $request->validate(
        [
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer'],
        ],
        [
            'answers.required' => 'Jawab seluruh soal sebelum menyelesaikan Pretest.',
            'answers.array' => 'Format jawaban tidak valid.',
            'answers.*.required' => 'Setiap soal wajib dijawab.',
            'answers.*.integer' => 'Pilihan jawaban tidak valid.',
        ]
    );

    $questions = $mission->pretestQuestions()
    ->where('is_active', true)
    ->with('options')
    ->orderBy('sequence')
    ->get();

    abort_if(
        $questions->isEmpty(),
        404,
        'Soal Pretest belum tersedia.'
    );

    foreach ($questions as $question) {
        if (! isset($validated['answers'][$question->id])) {
            throw ValidationException::withMessages([
                'answers' => 'Jawab seluruh soal sebelum menyelesaikan Pretest.',
            ]);
        }
    }

    DB::transaction(function () use (
        $attempt,
        $questions,
        $validated
    ): void {
        $correctAnswers = 0;

        foreach ($questions as $question) {
            $selectedOptionId = (int) $validated['answers'][$question->id];

            $selectedOption = $question->options
                ->firstWhere('id', $selectedOptionId);

            if (! $selectedOption) {
                throw ValidationException::withMessages([
                    'answers' => 'Terdapat pilihan jawaban yang tidak valid.',
                ]);
            }

            $isCorrect = (bool) $selectedOption->is_correct;

            $attempt->answers()->updateOrCreate(
                [
                    'pretest_question_id' => $question->id,
                ],
                [
                    'selected_option_id' => $selectedOption->id,
                    'is_correct' => $isCorrect,
                ]
            );

            if ($isCorrect) {
                $correctAnswers++;
            }
        }

        $totalQuestions = $questions->count();

        $score = round(
            ($correctAnswers / $totalQuestions) * 100,
            2
        );

        $attempt->update([
    'status' => PretestAttempt::STATUS_COMPLETED,
    'completed_at' => now(),
    'total_questions' => $totalQuestions,
    'correct_answers' => $correctAnswers,
    'score' => $score,
]);
    });

    return redirect()->route(
        'student.missions.pretest.show',
        ['mission' => $mission->slug]
    );
}
}
