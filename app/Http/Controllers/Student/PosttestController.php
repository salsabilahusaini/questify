<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PosttestAttempt;
use App\Services\BadgeAwardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosttestController extends Controller
{
    /**
     * Menampilkan halaman awal Posttest.
     */
    public function show(
        Request $request,
        Mission $mission
    ): View|RedirectResponse {
        abort_unless($mission->is_published, 404);

        $finalChallengeCompleted =
            FinalChallengeAttempt::query()
                ->where('mission_id', $mission->id)
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->exists();

        if (! $finalChallengeCompleted) {
            return redirect()
                ->route(
                    'student.missions.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Selesaikan Final Challenge terlebih dahulu.'
                );
        }

        $questionCount = $mission
            ->posttestQuestions()
            ->where('is_active', true)
            ->count();

        abort_if(
            $questionCount === 0,
            404,
            'Soal Posttest belum tersedia.'
        );

        $attempt = $mission
            ->posttestAttempts()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->first();

        return view('student.posttest.show', [
            'mission' => $mission,
            'questionCount' => $questionCount,
            'attempt' => $attempt,
        ]);
    }

    /**
     * Memulai pengerjaan Posttest siswa.
     */
    public function start(
    Request $request,
    Mission $mission
): RedirectResponse {
    abort_unless($mission->is_published, 404);

    $finalChallengeCompleted =
        FinalChallengeAttempt::query()
            ->where('mission_id', $mission->id)
            ->where(
                'user_id',
                $request->user()->id
            )
            ->where(
                'status',
                FinalChallengeAttempt::STATUS_COMPLETED
            )
            ->exists();

    if (! $finalChallengeCompleted) {
        return redirect()
            ->route(
                'student.missions.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'error',
                'Selesaikan Final Challenge terlebih dahulu.'
            );
    }

    $questionIds = $mission
        ->posttestQuestions()
        ->where('is_active', true)
        ->pluck('id');

    abort_if(
        $questionIds->isEmpty(),
        404,
        'Soal Posttest belum tersedia.'
    );

    $attempt = $mission
        ->posttestAttempts()
        ->firstOrCreate(
            [
                'user_id' => $request->user()->id,
            ],
            [
                'status' =>
                    PosttestAttempt::STATUS_IN_PROGRESS,
                'started_at' => now(),
                'total_questions' =>
                    $questionIds->count(),
                'correct_answers' => 0,
                'question_order' => $questionIds
                    ->shuffle()
                    ->values()
                    ->all(),
            ]
        );

    /*
     * Attempt lama yang masih berlangsung mungkin
     * belum memiliki urutan soal.
     */
    if (
        $attempt->status
            === PosttestAttempt::STATUS_IN_PROGRESS
        && empty($attempt->question_order)
    ) {
        $attempt->update([
            'total_questions' =>
                $questionIds->count(),
            'question_order' => $questionIds
                ->shuffle()
                ->values()
                ->all(),
        ]);
    }

    return redirect()->route(
        'student.missions.posttest.questions',
        ['mission' => $mission->slug]
    );
}
    /**
     * Menampilkan soal Posttest yang sedang dikerjakan.
     */
    public function questions(
        Request $request,
        Mission $mission
    ): View|RedirectResponse {
        abort_unless($mission->is_published, 404);

        $finalChallengeCompleted =
            FinalChallengeAttempt::query()
                ->where('mission_id', $mission->id)
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->exists();

        if (! $finalChallengeCompleted) {
            return redirect()
                ->route(
                    'student.missions.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Selesaikan Final Challenge terlebih dahulu.'
                );
        }

        $attempt = $mission
            ->posttestAttempts()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->first();

        if (! $attempt) {
            return redirect()
                ->route(
                    'student.missions.posttest.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Mulai Posttest terlebih dahulu.'
                );
        }

        if (
    $attempt->status
    === PosttestAttempt::STATUS_COMPLETED
) {
            return redirect()->route(
                'student.missions.posttest.show',
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
    $questionOrder = $mission
        ->posttestQuestions()
        ->where('is_active', true)
        ->pluck('id')
        ->shuffle()
        ->values();

    abort_if(
        $questionOrder->isEmpty(),
        404,
        'Soal Posttest belum tersedia.'
    );

    $attempt->update([
        'total_questions' => $questionOrder->count(),
        'question_order' => $questionOrder->all(),
    ]);
}

$questions = $mission
    ->posttestQuestions()
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
                    'posttest_question_id',
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
            'Soal Posttest belum tersedia.'
        );

        return view('student.posttest.questions', [
            'mission' => $mission,
            'attempt' => $attempt,
            'questions' => $questions,
        ]);
    }

    /**
     * Menyimpan jawaban dan menyelesaikan Posttest siswa.
     */
    public function submit(
    Request $request,
    Mission $mission,
    BadgeAwardService $badgeAwardService
): RedirectResponse {
        abort_unless($mission->is_published, 404);

        $finalChallengeCompleted =
            FinalChallengeAttempt::query()
                ->where('mission_id', $mission->id)
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->exists();

        if (! $finalChallengeCompleted) {
            return redirect()
                ->route(
                    'student.missions.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Selesaikan Final Challenge terlebih dahulu.'
                );
        }

        $attempt = $mission
            ->posttestAttempts()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->first();

        if (! $attempt) {
            return redirect()
                ->route(
                    'student.missions.posttest.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Mulai Posttest terlebih dahulu.'
                );
        }

        if (
    $attempt->status
    === PosttestAttempt::STATUS_COMPLETED
) {
            return redirect()->route(
                'student.missions.posttest.show',
                ['mission' => $mission->slug]
            );
        }

        $validated = $request->validate(
            [
                'answers' => [
                    'required',
                    'array',
                ],
                'answers.*' => [
                    'required',
                    'integer',
                ],
            ],
            [
                'answers.required' =>
                    'Jawab seluruh soal sebelum menyelesaikan Posttest.',
                'answers.array' =>
                    'Format jawaban tidak valid.',
                'answers.*.required' =>
                    'Setiap soal wajib dijawab.',
                'answers.*.integer' =>
                    'Pilihan jawaban tidak valid.',
            ]
        );

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
    $questionOrder = $mission
        ->posttestQuestions()
        ->where('is_active', true)
        ->pluck('id')
        ->shuffle()
        ->values();

    abort_if(
        $questionOrder->isEmpty(),
        404,
        'Soal Posttest belum tersedia.'
    );

    $attempt->update([
        'total_questions' => $questionOrder->count(),
        'question_order' => $questionOrder->all(),
    ]);
}

$questions = $mission
    ->posttestQuestions()
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
            'posttest_question_id',
            'option_text',
            'sequence',
            'is_correct',
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
            'Soal Posttest belum tersedia.'
        );

        foreach ($questions as $question) {
            if (
                ! isset(
                    $validated['answers'][$question->id]
                )
            ) {
                throw ValidationException::withMessages([
                    'answers' =>
                        'Jawab seluruh soal sebelum menyelesaikan Posttest.',
                ]);
            }
        }

        DB::transaction(function () use (
    $request,
    $mission,
    $attempt,
    $questions,
    $validated,
    $badgeAwardService
): void {
            $correctAnswers = 0;

            foreach ($questions as $question) {
                $selectedOptionId = (int) (
                    $validated['answers'][$question->id]
                );

                $selectedOption = $question
                    ->options
                    ->firstWhere(
                        'id',
                        $selectedOptionId
                    );

                if (! $selectedOption) {
                    throw ValidationException::withMessages([
                        'answers' =>
                            'Terdapat pilihan jawaban yang tidak valid.',
                    ]);
                }

                $isCorrect = (bool) (
                    $selectedOption->is_correct
                );

                $attempt->answers()->updateOrCreate(
                    [
                        'posttest_question_id' =>
                            $question->id,
                    ],
                    [
                        'selected_option_id' =>
                            $selectedOption->id,
                        'is_correct' => $isCorrect,
                    ]
                );

                if ($isCorrect) {
                    $correctAnswers++;
                }
            }

            $totalQuestions = $questions->count();

            $score = round(
                ($correctAnswers / $totalQuestions)
                    * 100,
                2
            );

            $attempt->update([
    'status' => PosttestAttempt::STATUS_COMPLETED,
    'completed_at' => now(),
    'total_questions' => $totalQuestions,
    'correct_answers' => $correctAnswers,
    'score' => $score,
]);
            MissionCompletion::firstOrCreate(
    [
        'user_id' => $request->user()->id,
        'mission_id' => $mission->id,
    ],
    [
        'earned_xp' => $mission->xp_reward,
        'completed_at' => now(),
    ]
);

$badgeAwardService->awardEligibleBadges(
    $request->user()
);
        });

        return redirect()
            ->route(
                'student.missions.posttest.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'success',
                'Posttest berhasil diselesaikan.'
            );
    }
}
