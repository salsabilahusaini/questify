<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinalChallengeController extends Controller
{
    /**
     * Menampilkan halaman Final Challenge.
     */
    public function show(
        Request $request,
        Mission $mission
    ): View|RedirectResponse {
        abort_unless($mission->is_published, 404);

        $quizCompleted = QuizAttempt::query()
            ->where('mission_id', $mission->id)
            ->where('user_id', $request->user()->id)
            ->where(
                'status',
                QuizAttempt::STATUS_COMPLETED
            )
            ->exists();

        if (! $quizCompleted) {
            return redirect()
                ->route(
                    'student.missions.show',
                    ['mission' => $mission->slug]
                )
                ->with(
                    'error',
                    'Selesaikan Kuis Materi terlebih dahulu.'
                );
        }

        $challenge = $mission
            ->finalChallenge()
            ->where('is_active', true)
            ->first();

        abort_if(
            $challenge === null,
            404,
            'Final Challenge belum tersedia.'
        );

        $attempt = FinalChallengeAttempt::query()
            ->where('user_id', $request->user()->id)
            ->where('final_challenge_id', $challenge->id)
            ->first();

        return view('student.final-challenge.show', [
            'mission' => $mission,
            'challenge' => $challenge,
            'attempt' => $attempt,
        ]);
    }
    /**
 * Menilai dan menyimpan jawaban Final Challenge.
 */
public function submit(
    Request $request,
    Mission $mission
): RedirectResponse {
    abort_unless($mission->is_published, 404);

    $quizCompleted = QuizAttempt::query()
        ->where('mission_id', $mission->id)
        ->where('user_id', $request->user()->id)
        ->where(
            'status',
            QuizAttempt::STATUS_COMPLETED
        )
        ->exists();

    if (! $quizCompleted) {
        return redirect()
            ->route(
                'student.missions.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'error',
                'Selesaikan Kuis Materi terlebih dahulu.'
            );
    }

    $challenge = $mission
        ->finalChallenge()
        ->where('is_active', true)
        ->first();

    abort_if(
        $challenge === null,
        404,
        'Final Challenge belum tersedia.'
    );

    $existingAttempt = FinalChallengeAttempt::query()
        ->where('user_id', $request->user()->id)
        ->where('final_challenge_id', $challenge->id)
        ->first();

    if ($existingAttempt?->isCompleted()) {
        return redirect()
            ->route(
                'student.missions.final-challenge.show',
                ['mission' => $mission->slug]
            )
            ->with(
                'info',
                'Final Challenge pada misi ini sudah diselesaikan.'
            );
    }

    $validated = $request->validate(
        [
            'answer_code' => [
                'required',
                'string',
                'max:5000',
            ],
            'answer_output' => [
                'required',
                'string',
                'max:1000',
            ],
            'answer_explanation' => [
                'required',
                'string',
                'min:30',
                'max:5000',
            ],
        ],
        [
            'answer_code.required' =>
                'Kode perbaikan harus diisi.',
            'answer_output.required' =>
                'Output program harus diisi.',
            'answer_explanation.required' =>
                'Penjelasan analisis harus diisi.',
            'answer_explanation.min' =>
                'Penjelasan analisis minimal 30 karakter.',
        ]
    );

    /*
     * Menghapus spasi agar variasi format penulisan kode
     * tidak memengaruhi pemeriksaan jawaban.
     */
    $normalizedCode = strtolower(
        preg_replace(
            '/\s+/',
            '',
            $validated['answer_code']
        ) ?? ''
    );

    /*
     * Komponen kode yang dinilai:
     * nilai awal, kondisi, iterasi, dan perintah output.
     */
    $initializationCorrect =
        preg_match('/\$i=2/', $normalizedCode) === 1;

    $conditionCorrect =
        preg_match('/\$i<=20/', $normalizedCode) === 1;

    $iterationCorrect =
        preg_match('/\$i\+=2/', $normalizedCode) === 1
        || preg_match(
            '/\$i=\$i\+2/',
            $normalizedCode
        ) === 1;

    $outputCommandCorrect =
        str_contains($normalizedCode, 'echo$i');

    $codeScore = 0;

    if ($initializationCorrect) {
        $codeScore += 10;
    }

    if ($conditionCorrect) {
        $codeScore += 15;
    }

    if ($iterationCorrect) {
        $codeScore += 20;
    }

    if ($outputCommandCorrect) {
        $codeScore += 5;
    }

    /*
     * Menormalkan output agar pemisah spasi atau koma
     * tetap dapat dibandingkan secara konsisten.
     */
    $answerOutput = strtolower(
        trim(
            preg_replace(
                '/[\s,]+/',
                ' ',
                $validated['answer_output']
            ) ?? ''
        )
    );

    $expectedOutput = strtolower(
        trim(
            preg_replace(
                '/[\s,]+/',
                ' ',
                $challenge->expected_output ?? ''
            ) ?? ''
        )
    );

    $outputScore = $answerOutput === $expectedOutput
        ? 25
        : 0;

    /*
     * Penjelasan dinilai berdasarkan konsep penting
     * yang seharusnya ditemukan siswa.
     */
    $explanation = strtolower(
        $validated['answer_explanation']
    );

    $mentionsWrongDirection =
        str_contains($explanation, '$i--')
        || str_contains($explanation, 'berkurang')
        || str_contains($explanation, 'decrement');

    $mentionsInfiniteLoop =
        str_contains($explanation, 'tidak berhenti')
        || str_contains($explanation, 'terus berjalan')
        || str_contains($explanation, 'infinite loop');

    $mentionsCorrectIteration =
        str_contains($explanation, '$i += 2')
        || str_contains($explanation, '$i+=2')
        || str_contains($explanation, 'bertambah 2')
        || str_contains($explanation, 'increment 2');

    $mentionsEvenPattern =
        str_contains($explanation, 'genap')
        || str_contains($explanation, 'kelipatan 2');

    $explanationIndicators = [
        $mentionsWrongDirection,
        $mentionsInfiniteLoop,
        $mentionsCorrectIteration,
        $mentionsEvenPattern,
    ];

    $explanationScore =
        count(array_filter($explanationIndicators))
        * 6.25;

    $score = round(
        $codeScore
        + $outputScore
        + $explanationScore,
        2
    );

    $earnedPoints = (int) round(
        ($score / 100)
        * $challenge->max_points
    );

    $feedback = match (true) {
        $score >= 100 =>
            'Sangat baik. Kode, output, dan penjelasanmu sudah menunjukkan pemahaman yang lengkap mengenai pola perulangan dan kondisi berhenti.',

        $score >= 75 =>
            'Jawabanmu sudah baik. Periksa kembali salah satu komponen kode, output, atau kelengkapan alasan perbaikannya.',

        $score >= 50 =>
            'Sebagian konsep sudah tepat. Perhatikan kembali arah perubahan variabel pengontrol dan hubungannya dengan kondisi berhenti.',

        default =>
            'Pelajari kembali nilai awal, kondisi, iterasi, serta pola bilangan genap sebelum menyusun perbaikan program.',
    };

    DB::transaction(function () use (
        $request,
        $mission,
        $challenge,
        $validated,
        $score,
        $earnedPoints,
        $feedback
    ): void {
        $attempt = FinalChallengeAttempt::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'final_challenge_id' => $challenge->id,
            ],
            [
                'mission_id' => $mission->id,
                'status' =>
                    FinalChallengeAttempt::STATUS_IN_PROGRESS,
                'started_at' => now(),
            ]
        );

        $attempt->update([
            'mission_id' => $mission->id,
            'status' =>
                FinalChallengeAttempt::STATUS_COMPLETED,
            'answer_code' =>
                $validated['answer_code'],
            'answer_output' =>
                $validated['answer_output'],
            'answer_explanation' =>
                $validated['answer_explanation'],
            'completed_at' => now(),
            'score' => $score,
            'earned_points' => $earnedPoints,
            'feedback' => $feedback,
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
    });

    return redirect()
        ->route(
            'student.missions.final-challenge.show',
            ['mission' => $mission->slug]
        )
        ->with(
            'success',
            'Final Challenge berhasil diselesaikan.'
        );
}
}
