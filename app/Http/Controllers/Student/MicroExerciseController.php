<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseQuestion;
use App\Models\MicroExerciseAnswer;
use App\Models\MicroExerciseAttempt;
use App\Models\MicroExerciseOption;
use App\Models\PretestAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MicroExerciseController extends Controller
{
    /**
     * Menampilkan halaman Latihan Mikro.
     */
    public function show(
        Request $request,
        Mission $mission
    ): View|RedirectResponse {
        abort_unless($mission->is_published, 404);

        $pretestCompleted = $mission->pretestAttempts()
    ->where('user_id', $request->user()->id)
    ->where(
        'status',
        PretestAttempt::STATUS_COMPLETED
    )
    ->exists();

        $requiredBlockIds = MaterialBlock::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->whereHas(
                'materialSection',
                function ($query) use ($mission) {
                    $query
                        ->where('mission_id', $mission->id)
                        ->where('is_active', true);
                }
            )
            ->pluck('id');

        $completedRequiredBlocks =
            MaterialBlockProgress::query()
                ->where('user_id', $request->user()->id)
                ->whereIn(
                    'material_block_id',
                    $requiredBlockIds
                )
                ->where('is_completed', true)
                ->count();

        $materialCompleted = $pretestCompleted
            && $requiredBlockIds->isNotEmpty()
            && $completedRequiredBlocks
                === $requiredBlockIds->count();

        if (! $materialCompleted) {
            return redirect()
                ->route(
                    'student.missions.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Selesaikan seluruh Materi Prakelas terlebih dahulu.'
                );
        }

        $questions = MicroExerciseQuestion::query()
            ->where('is_active', true)
            ->whereHas(
                'materialBlock.materialSection',
                function ($query) use ($mission) {
                    $query
                        ->where('mission_id', $mission->id)
                        ->where('is_active', true);
                }
            )
            ->with([
                'materialBlock:id,material_section_id,title,sequence',
                'options' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'micro_exercise_question_id',
                            'option_text',
                            'sequence',
                        ])
                        ->orderBy('sequence');
                },
            ])
            ->orderBy('material_block_id')
            ->orderBy('sequence')
            ->get();

        abort_if(
            $questions->isEmpty(),
            404,
            'Latihan Mikro belum tersedia.'
        );

        $attempt = $mission->microExerciseAttempts()
            ->where('user_id', $request->user()->id)
            ->first();

        return view('student.micro-exercise.show', [
            'mission' => $mission,
            'questions' => $questions,
            'attempt' => $attempt,
        ]);
    }
    /**
 * Menilai dan menyimpan jawaban Latihan Mikro.
 */
public function submit(
    Request $request,
    Mission $mission
): RedirectResponse {
    abort_unless($mission->is_published, 404);

    $pretestCompleted = $mission->pretestAttempts()
        ->where('user_id', $request->user()->id)
        ->where(
            'status',
            PretestAttempt::STATUS_COMPLETED
        )
        ->exists();

    $requiredBlockIds = MaterialBlock::query()
        ->where('is_active', true)
        ->where('is_required', true)
        ->whereHas(
            'materialSection',
            function ($query) use ($mission) {
                $query
                    ->where('mission_id', $mission->id)
                    ->where('is_active', true);
            }
        )
        ->pluck('id');

    $completedRequiredBlocks =
        MaterialBlockProgress::query()
            ->where('user_id', $request->user()->id)
            ->whereIn(
                'material_block_id',
                $requiredBlockIds
            )
            ->where('is_completed', true)
            ->count();

    $materialCompleted = $pretestCompleted
        && $requiredBlockIds->isNotEmpty()
        && $completedRequiredBlocks
            === $requiredBlockIds->count();

    if (! $materialCompleted) {
        return redirect()
            ->route(
                'student.missions.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'error',
                'Selesaikan seluruh Materi Prakelas terlebih dahulu.'
            );
    }


    $questions = MicroExerciseQuestion::query()
        ->where('is_active', true)
        ->whereHas(
            'materialBlock.materialSection',
            function ($query) use ($mission) {
                $query
                    ->where('mission_id', $mission->id)
                    ->where('is_active', true);
            }
        )
        ->with([
            'options' => function ($query) {
                $query->orderBy('sequence');
            },
        ])
        ->orderBy('material_block_id')
        ->orderBy('sequence')
        ->get();

    abort_if(
        $questions->isEmpty(),
        404,
        'Latihan Mikro belum tersedia.'
    );

    $validated = $request->validate([
        'answers' => [
            'required',
            'array',
        ],
        'answers.*' => [
            'required',
            'integer',
            'exists:micro_exercise_options,id',
        ],
    ], [
        'answers.required' =>
            'Pilih jawaban pada seluruh soal terlebih dahulu.',
        'answers.*.required' =>
            'Setiap soal harus dijawab.',
    ]);

    $questionIds = $questions->pluck('id');

    $submittedQuestionIds = collect(
        array_keys($validated['answers'])
    )->map(
        fn ($questionId) => (int) $questionId
    );

    if (
        $submittedQuestionIds->sort()->values()->all()
        !== $questionIds->sort()->values()->all()
    ) {
        throw ValidationException::withMessages([
            'answers' =>
                'Jawaban yang dikirim tidak sesuai dengan daftar soal.',
        ]);
    }

    $existingAttempt = MicroExerciseAttempt::query()
    ->where('mission_id', $mission->id)
    ->where('user_id', $request->user()->id)
    ->first();

    if ($existingAttempt?->isCompleted()) {
        return redirect()
            ->route(
                'student.missions.micro-exercise.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'info',
                'Latihan Mikro pada misi ini sudah diselesaikan.'
            );
    }

    DB::transaction(function () use (
        $request,
        $mission,
        $questions,
        $validated
    ): void {
        $attempt = MicroExerciseAttempt::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'mission_id' => $mission->id,
            ],
            [
                'status' =>
                    MicroExerciseAttempt::STATUS_IN_PROGRESS,
                'started_at' => now(),
                'completed_at' => null,
                'total_questions' => $questions->count(),
                'correct_answers' => 0,
                'score' => null,
                'earned_points' => 0,
            ]
        );

        $attempt->answers()->delete();

        $correctAnswers = 0;
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $selectedOptionId = (int) $validated['answers'][
                $question->id
            ];

            $selectedOption = MicroExerciseOption::query()
                ->where('id', $selectedOptionId)
                ->where(
                    'micro_exercise_question_id',
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

            MicroExerciseAnswer::create([
                'micro_exercise_attempt_id' => $attempt->id,
                'micro_exercise_question_id' => $question->id,
                'selected_option_id' => $selectedOption->id,
                'is_correct' => $isCorrect,
                'awarded_points' => $awardedPoints,
                'answered_at' => now(),
            ]);
        }

        $score = $questions->isNotEmpty()
            ? round(
                ($correctAnswers / $questions->count()) * 100,
                2
            )
            : 0;

        $attempt->update([
            'status' =>
                MicroExerciseAttempt::STATUS_COMPLETED,
            'completed_at' => now(),
            'total_questions' => $questions->count(),
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'earned_points' => $earnedPoints,
        ]);
    });

    return redirect()
        ->route(
            'student.missions.micro-exercise.show',
            ['mission' => $mission->slug]
        )
        ->with(
            'success',
            'Latihan Mikro berhasil diselesaikan.'
        );
}
}
