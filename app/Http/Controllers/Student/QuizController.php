<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Menampilkan halaman Kuis Materi.
     */
    public function show(
        Request $request,
        Mission $mission
    ): View|RedirectResponse {
        abort_unless($mission->is_published, 404);

        $microExerciseCompleted =
            MicroExerciseAttempt::query()
                ->where('mission_id', $mission->id)
                ->where('user_id', $request->user()->id)
                ->where(
                    'status',
                    MicroExerciseAttempt::STATUS_COMPLETED
                )
                ->exists();

        if (! $microExerciseCompleted) {
            return redirect()
                ->route(
                    'student.missions.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Selesaikan Latihan Mikro terlebih dahulu.'
                );
        }

        $questions = QuizQuestion::query()
            ->where('mission_id', $mission->id)
            ->where('is_active', true)
            ->with([
                'options' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'quiz_question_id',
                            'option_text',
                            'sequence',
                        ])
                        ->orderBy('sequence');
                },
            ])
            ->orderBy('sequence')
            ->get();

        abort_if(
            $questions->isEmpty(),
            404,
            'Kuis Materi belum tersedia.'
        );

        $attempt = QuizAttempt::query()
            ->where('mission_id', $mission->id)
            ->where('user_id', $request->user()->id)
            ->first();

        return view('student.quiz.show', [
            'mission' => $mission,
            'questions' => $questions,
            'attempt' => $attempt,
        ]);
    }
    /**
 * Menilai dan menyimpan jawaban Kuis Materi.
 */
public function submit(
    Request $request,
    Mission $mission
): RedirectResponse {
    abort_unless($mission->is_published, 404);

    $microExerciseCompleted =
        MicroExerciseAttempt::query()
            ->where('mission_id', $mission->id)
            ->where('user_id', $request->user()->id)
            ->where(
                'status',
                MicroExerciseAttempt::STATUS_COMPLETED
            )
            ->exists();

    if (! $microExerciseCompleted) {
        return redirect()
            ->route(
                'student.missions.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'error',
                'Selesaikan Latihan Mikro terlebih dahulu.'
            );
    }

    $questions = QuizQuestion::query()
        ->where('mission_id', $mission->id)
        ->where('is_active', true)
        ->with([
            'options' => function ($query) {
                $query->orderBy('sequence');
            },
        ])
        ->orderBy('sequence')
        ->get();

    abort_if(
        $questions->isEmpty(),
        404,
        'Kuis Materi belum tersedia.'
    );

    $validated = $request->validate(
        [
            'answers' => [
                'required',
                'array',
            ],
            'answers.*' => [
                'required',
                'integer',
                'exists:quiz_options,id',
            ],
        ],
        [
            'answers.required' =>
                'Pilih jawaban pada seluruh soal terlebih dahulu.',
            'answers.*.required' =>
                'Setiap soal harus dijawab.',
        ]
    );

    $questionIds = $questions->pluck('id');

    $submittedQuestionIds = collect(
        array_keys($validated['answers'])
    )->map(
        fn ($questionId) => (int) $questionId
    );

    if (
        $submittedQuestionIds
            ->sort()
            ->values()
            ->all()
        !==
        $questionIds
            ->sort()
            ->values()
            ->all()
    ) {
        throw ValidationException::withMessages([
            'answers' =>
                'Jawaban yang dikirim tidak sesuai dengan daftar soal.',
        ]);
    }

    $existingAttempt = QuizAttempt::query()
        ->where('mission_id', $mission->id)
        ->where('user_id', $request->user()->id)
        ->first();

    if ($existingAttempt?->isCompleted()) {
        return redirect()
            ->route(
                'student.missions.quiz.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'info',
                'Kuis Materi pada misi ini sudah diselesaikan.'
            );
    }

    DB::transaction(function () use (
        $request,
        $mission,
        $questions,
        $validated
    ): void {
        $attempt = QuizAttempt::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'mission_id' => $mission->id,
            ],
            [
                'status' => QuizAttempt::STATUS_IN_PROGRESS,
                'started_at' => now(),
            ]
        );

        $attempt->answers()->delete();

        $correctAnswers = 0;
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $selectedOptionId = (int)
                $validated['answers'][$question->id];

            $selectedOption = QuizOption::query()
                ->where('id', $selectedOptionId)
                ->where(
                    'quiz_question_id',
                    $question->id
                )
                ->first();

            if (! $selectedOption) {
                throw ValidationException::withMessages([
                    'answers' =>
                        'Terdapat pilihan jawaban yang tidak sesuai dengan soal.',
                ]);
            }

            $isCorrect = $selectedOption->is_correct;

            $awardedPoints = $isCorrect
                ? $question->points
                : 0;

            if ($isCorrect) {
                $correctAnswers++;
            }

            $earnedPoints += $awardedPoints;

            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $question->id,
                'selected_option_id' =>
                    $selectedOption->id,
                'is_correct' => $isCorrect,
                'awarded_points' => $awardedPoints,
                'answered_at' => now(),
            ]);
        }

        $score = round(
            (
                $correctAnswers /
                $questions->count()
            ) * 100,
            2
        );

        $attempt->update([
            'status' => QuizAttempt::STATUS_COMPLETED,
            'completed_at' => now(),
            'total_questions' => $questions->count(),
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'earned_points' => $earnedPoints,
        ]);
    });

    return redirect()
        ->route(
            'student.missions.quiz.show',
            ['mission' => $mission->slug]
        )
        ->with(
            'success',
            'Kuis Materi berhasil diselesaikan.'
        );
}
}
