<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Mission;
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

    $questionCount = $mission->pretestQuestions()
        ->where('is_active', true)
        ->count();

    abort_if(
        $questionCount === 0,
        404,
        'Soal Pretest belum tersedia.'
    );

    $attempt = $mission->pretestAttempts()->firstOrCreate(
        [
            'user_id' => $request->user()->id,
        ],
        [
            'status' => 'in_progress',
            'started_at' => now(),
            'total_questions' => $questionCount,
            'correct_answers' => 0,
        ]
    );

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

    if ($attempt->status === 'completed') {
        return redirect()->route(
            'student.missions.pretest.show',
            ['mission' => $mission->slug]
        );
    }

    $questions = $mission->pretestQuestions()
        ->where('is_active', true)
        ->select([
            'id',
            'mission_id',
            'question_text',
            'sequence',
        ])
        ->with([
            'options' => function ($query) {
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

    if ($attempt->status === 'completed') {
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
            'status' => 'completed',
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
