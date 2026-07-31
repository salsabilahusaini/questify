<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
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

        $questionCount = $mission
            ->posttestQuestions()
            ->where('is_active', true)
            ->count();

        abort_if(
            $questionCount === 0,
            404,
            'Soal Posttest belum tersedia.'
        );

        $mission->posttestAttempts()->firstOrCreate(
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

        if ($attempt->status === 'completed') {
            return redirect()->route(
                'student.missions.posttest.show',
                ['mission' => $mission->slug]
            );
        }

        $questions = $mission
            ->posttestQuestions()
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
                            'posttest_question_id',
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

        if ($attempt->status === 'completed') {
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

        $questions = $mission
            ->posttestQuestions()
            ->where('is_active', true)
            ->with('options')
            ->get();

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
                'status' => 'completed',
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
