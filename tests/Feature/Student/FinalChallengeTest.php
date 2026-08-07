<?php

namespace Tests\Feature\Student;

use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalChallengeTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Mission $mission;

    private FinalChallenge $challenge;

    /**
     * Menyiapkan siswa, misi, dan Final Challenge
     * untuk setiap pengujian.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $this->mission = Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'final-challenge-perulangan-test',
            'description' =>
                'Misi pengujian Final Challenge.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $this->challenge = FinalChallenge::create([
            'mission_id' => $this->mission->id,
            'title' =>
                'Perbaiki Program Nomor Antrean Genap',
            'scenario' =>
                'Perbaiki program agar menampilkan bilangan genap dari 2 sampai 20.',
            'instructions' =>
                'Perbaiki kode, tentukan output, dan jelaskan alasan perbaikannya.',
            'starter_code' => <<<'CODE'
for ($i = 2; $i <= 20; $i--) {
    echo $i . " ";
}
CODE,
            'expected_output' =>
                '2 4 6 8 10 12 14 16 18 20',
            'max_points' => 100,
            'is_active' => true,
        ]);
    }
    /**
 * Halaman Final Challenge tidak dapat dibuka
 * sebelum Kuis Materi diselesaikan.
 */
public function test_final_challenge_page_is_rejected_before_quiz_is_completed(): void
{
    $response = $this
        ->actingAs($this->student)
        ->get(
            route(
                'student.missions.final-challenge.show',
                [
                    'mission' => $this->mission->slug,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'student.missions.show',
            [
                'mission' => $this->mission->slug,
            ]
        )
    );

    $response->assertSessionHas(
        'error',
        'Selesaikan Kuis Materi terlebih dahulu.'
    );
}

    /**
     * Request langsung menuju submit ditolak
     * jika Kuis Materi belum diselesaikan.
     */
    public function test_direct_submission_is_rejected_before_quiz_is_completed(): void
    {
        $response = $this
            ->actingAs($this->student)
            ->post(
                route(
                    'student.missions.final-challenge.submit',
                    [
                        'mission' => $this->mission->slug,
                    ]
                ),
                $this->correctAnswer()
            );

        $response->assertRedirect(
            route(
                'student.missions.show',
                [
                    'mission' => $this->mission->slug,
                ]
            )
        );

        $response->assertSessionHas(
            'error',
            'Selesaikan Kuis Materi terlebih dahulu.'
        );

        $this->assertDatabaseMissing(
            'final_challenge_attempts',
            [
                'user_id' => $this->student->id,
                'final_challenge_id' =>
                    $this->challenge->id,
            ]
        );
    }

    /**
     * Siswa dapat membuka Final Challenge
     * setelah Kuis Materi diselesaikan.
     */
    public function test_student_can_view_final_challenge_after_quiz_is_completed(): void
    {
        $this->completeQuiz();

        $response = $this
            ->actingAs($this->student)
            ->get(
                route(
                    'student.missions.final-challenge.show',
                    [
                        'mission' => $this->mission->slug,
                    ]
                )
            );

        $response->assertOk();

        $response->assertSee(
            'Perbaiki Program Nomor Antrean Genap'
        );

        $response->assertSee(
            'Perbaiki program agar menampilkan bilangan genap dari 2 sampai 20.'
        );

        $response->assertSee(
            'for ($i = 2; $i <= 20; $i--)'
        );
    }

    /**
     * Seluruh kolom jawaban Final Challenge
     * harus memenuhi aturan validasi.
     */
    public function test_final_challenge_answer_must_be_valid(): void
    {
        $this->completeQuiz();

        $showRoute = route(
            'student.missions.final-challenge.show',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->from($showRoute)
            ->post(
                route(
                    'student.missions.final-challenge.submit',
                    [
                        'mission' => $this->mission->slug,
                    ]
                ),
                [
                    'answer_code' => '',
                    'answer_output' => '',
                    'answer_explanation' => 'Terlalu singkat.',
                ]
            );

        $response->assertRedirect($showRoute);

        $response->assertSessionHasErrors([
            'answer_code',
            'answer_output',
            'answer_explanation',
        ]);

        $this->assertDatabaseMissing(
            'final_challenge_attempts',
            [
                'user_id' => $this->student->id,
                'final_challenge_id' =>
                    $this->challenge->id,
            ]
        );
    }

    /**
     * Jawaban lengkap memperoleh nilai penuh,
     * menyimpan attempt, dan belum menyelesaikan misi.
     */
    public function test_correct_answer_receives_full_score_without_completing_mission(): void
    {
        $this->completeQuiz();

        $submitRoute = route(
            'student.missions.final-challenge.submit',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $showRoute = route(
            'student.missions.final-challenge.show',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->post(
                $submitRoute,
                $this->correctAnswer()
            );

        $response->assertRedirect($showRoute);

        $response->assertSessionHas(
            'success',
            'Final Challenge berhasil diselesaikan.'
        );

        $attempt = FinalChallengeAttempt::query()
            ->where('user_id', $this->student->id)
            ->where(
                'final_challenge_id',
                $this->challenge->id
            )
            ->firstOrFail();

        $this->assertSame(
            FinalChallengeAttempt::STATUS_COMPLETED,
            $attempt->status
        );

        $this->assertSame(
            $this->mission->id,
            $attempt->mission_id
        );

        $this->assertSame(
            '100.00',
            $attempt->score
        );

        $this->assertSame(
            100,
            $attempt->earned_points
        );

        $this->assertNotNull(
            $attempt->completed_at
        );

        /*
         * Final Challenge tidak boleh langsung
         * menyelesaikan misi atau memberikan XP.
         */
        $this->assertDatabaseMissing(
            'mission_completions',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
            ]
        );

        /*
         * Pengiriman ulang tidak boleh membuat
         * attempt Final Challenge menjadi ganda.
         */
        $this
            ->actingAs($this->student)
            ->post(
                $submitRoute,
                $this->correctAnswer()
            )
            ->assertRedirect($showRoute);

        $attemptCount = FinalChallengeAttempt::query()
            ->where('user_id', $this->student->id)
            ->where(
                'final_challenge_id',
                $this->challenge->id
            )
            ->count();

        $this->assertSame(
            1,
            $attemptCount
        );

        $this->assertDatabaseMissing(
            'mission_completions',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
            ]
        );
    }

    /**
     * Membuat Kuis Materi siswa berstatus selesai.
     */
    private function completeQuiz(): void
    {
        QuizAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
            'earned_points' => 20,
        ]);
    }

    /**
     * Menyediakan jawaban yang memenuhi seluruh
     * indikator penilaian Final Challenge.
     *
     * @return array{
     *     answer_code: string,
     *     answer_output: string,
     *     answer_explanation: string
     * }
     */
    private function correctAnswer(): array
    {
        return [
            'answer_code' => <<<'CODE'
for ($i = 2; $i <= 20; $i += 2) {
    echo $i . " ";
}
CODE,
            /*
             * Menggunakan koma untuk memastikan
             * normalisasi output tetap bekerja.
             */
            'answer_output' =>
                '2, 4, 6, 8, 10, 12, 14, 16, 18, 20',

            'answer_explanation' =>
                'Penggunaan $i-- membuat nilai variabel berkurang sehingga kondisi $i <= 20 terus terpenuhi dan perulangan tidak berhenti. Iterasi harus diubah menjadi $i += 2 agar nilai bertambah 2 dan menghasilkan pola bilangan genap.',
        ];
    }
}
