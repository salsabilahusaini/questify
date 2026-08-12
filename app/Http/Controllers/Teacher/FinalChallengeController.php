<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinalChallengeController extends Controller
{
    /**
     * Menampilkan informasi Final Challenge
     * pada sebuah misi.
     */
    public function index(Mission $mission): View
    {
        $challenge = $mission
            ->finalChallenge()
            ->withCount('attempts')
            ->first();

        $attemptCount = 0;
        $completedAttemptCount = 0;
        $highestScore = null;

        if ($challenge !== null) {
            $attemptCount = (int) $challenge->attempts_count;

            $completedAttemptCount = $challenge
                ->attempts()
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->count();

            $highestScore = $challenge
                ->attempts()
                ->whereNotNull('score')
                ->max('score');
        }

        return view(
            'teacher.missions.final-challenge.index',
            [
                'mission' => $mission,
                'challenge' => $challenge,
                'attemptCount' => $attemptCount,
                'completedAttemptCount' =>
                    $completedAttemptCount,
                'highestScore' => $highestScore,
            ]
        );
    }
    /**
 * Menampilkan formulir tambah Final Challenge.
 */
public function create(
    Mission $mission
): View|RedirectResponse {
    /*
     * Setiap misi hanya boleh mempunyai
     * satu Final Challenge.
     */
    if ($mission->finalChallenge()->exists()) {
        return redirect()
            ->route(
                'teacher.missions.final-challenge.index',
                $mission
            )
            ->with(
                'error',
                'Misi ini sudah memiliki Final Challenge. Gunakan menu Edit Final Challenge untuk mengubahnya.'
            );
    }

    return view(
        'teacher.missions.final-challenge.create',
        [
            'mission' => $mission,
        ]
    );
}
/**
 * Menyimpan Final Challenge baru pada misi.
 */
public function store(
    Request $request,
    Mission $mission
): RedirectResponse {
    /*
     * Setiap misi hanya boleh mempunyai
     * satu Final Challenge.
     */
    if ($mission->finalChallenge()->exists()) {
        return redirect()
            ->route(
                'teacher.missions.final-challenge.index',
                $mission
            )
            ->with(
                'error',
                'Misi ini sudah memiliki Final Challenge. Gunakan menu Edit Final Challenge untuk mengubahnya.'
            );
    }

    $validated = $request->validate([
        'title' => [
            'required',
            'string',
            'max:255',
        ],
        'scenario' => [
            'required',
            'string',
            'max:10000',
        ],
        'instructions' => [
            'required',
            'string',
            'max:10000',
        ],
        'starter_code' => [
            'nullable',
            'string',
            'max:20000',
        ],
        'expected_output' => [
            'nullable',
            'string',
            'max:10000',
        ],
        'max_points' => [
            'required',
            'integer',
            'min:0',
            'max:10000',
        ],
    ]);

    $mission->finalChallenge()->create([
        'title' => $validated['title'],
        'scenario' => $validated['scenario'],
        'instructions' => $validated['instructions'],
        'starter_code' =>
            $validated['starter_code'] ?? null,
        'expected_output' =>
            $validated['expected_output'] ?? null,
        'max_points' => $validated['max_points'],
        'is_active' =>
            $request->boolean('is_active'),
    ]);

    return redirect()
        ->route(
            'teacher.missions.final-challenge.index',
            $mission
        )
        ->with(
            'success',
            'Final Challenge berhasil ditambahkan.'
        );
}

    /**
     * Menampilkan formulir edit Final Challenge.
     */
    public function edit(
        Mission $mission,
        FinalChallenge $finalChallenge
    ): View {
        $this->ensureChallengeBelongsToMission(
            $mission,
            $finalChallenge
        );

        return view(
            'teacher.missions.final-challenge.edit',
            [
                'mission' => $mission,
                'challenge' => $finalChallenge,
            ]
        );
    }

    /**
     * Menyimpan perubahan Final Challenge.
     */
    public function update(
        Request $request,
        Mission $mission,
        FinalChallenge $finalChallenge
    ): RedirectResponse {
        $this->ensureChallengeBelongsToMission(
            $mission,
            $finalChallenge
        );
        $hasStudentAttempt = FinalChallengeAttempt::query()
    ->where(
        'final_challenge_id',
        $finalChallenge->id
    )
    ->exists();

if ($hasStudentAttempt) {
    return redirect()
        ->route(
            'teacher.missions.final-challenge.edit',
            [
                'mission' => $mission,
                'finalChallenge' => $finalChallenge,
            ]
        )
        ->with(
            'error',
            'Final Challenge tidak dapat diubah karena sudah ada peserta didik yang memulai Final Challenge.'
        );
}

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'scenario' => [
                'required',
                'string',
                'max:10000',
            ],
            'instructions' => [
                'required',
                'string',
                'max:10000',
            ],
            'starter_code' => [
                'nullable',
                'string',
                'max:20000',
            ],
            'expected_output' => [
                'nullable',
                'string',
                'max:10000',
            ],
            'max_points' => [
                'required',
                'integer',
                'min:0',
                'max:10000',
            ],
        ]);

        $finalChallenge->update([
            'title' => $validated['title'],
            'scenario' => $validated['scenario'],
            'instructions' => $validated['instructions'],
            'starter_code' =>
                $validated['starter_code'] ?? null,
            'expected_output' =>
                $validated['expected_output'] ?? null,
            'max_points' => $validated['max_points'],
            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route(
                'teacher.missions.final-challenge.index',
                $mission
            )
            ->with(
                'success',
                'Final Challenge berhasil diperbarui.'
            );
    }

    /**
     * Memastikan Final Challenge berasal dari
     * misi yang sedang dikelola.
     */
    private function ensureChallengeBelongsToMission(
        Mission $mission,
        FinalChallenge $finalChallenge
    ): void {
        abort_unless(
            (int) $finalChallenge->mission_id
                === (int) $mission->id,
            404
        );
    }
}
