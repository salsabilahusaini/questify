<?php

namespace Tests\Feature\Student;

use App\Models\Mission;
use App\Models\PretestAttempt;
use App\Models\PretestOption;
use App\Models\PretestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PretestTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Mission $mission;

    private PretestAttempt $pretestAttempt;

    /**
     * Menyiapkan siswa, misi, dan pengerjaan Pretest
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
            'slug' => 'memahami-konsep-perulangan-pretest-test',
            'description' => 'Misi pengujian Pretest.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $this->pretestAttempt = PretestAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'status' => PretestAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'total_questions' => 0,
            'correct_answers' => 0,
        ]);
    }

    /**
 * Soal Pretest ditampilkan berdasarkan urutan
 * yang tersimpan pada attempt siswa.
 */
public function test_pretest_questions_follow_saved_question_order(): void
{
    [$firstQuestion] = $this->createQuestion(
        sequence: 1,
        questionText: 'Pertanyaan pertama tentang perulangan.'
    );

    [$secondQuestion] = $this->createQuestion(
        sequence: 2,
        questionText: 'Pertanyaan kedua tentang perulangan.'
    );

    /*
     * Urutan sengaja dibuat berbeda dari sequence
     * untuk memastikan controller memakai question_order.
     */
    $this->pretestAttempt->update([
        'total_questions' => 2,
        'question_order' => [
            $secondQuestion->id,
            $firstQuestion->id,
        ],
    ]);

    $response = $this
        ->actingAs($this->student)
        ->get(
            route(
                'student.missions.pretest.questions',
                [
                    'mission' => $this->mission->slug,
                ]
            )
        );

    $response->assertOk();

    $response->assertSeeInOrder([
        'Pertanyaan kedua tentang perulangan.',
        'Pertanyaan pertama tentang perulangan.',
    ]);
}

    /**
     * Siswa wajib menjawab seluruh soal
     * sebelum menyelesaikan Pretest.
     */
    public function test_student_must_answer_all_pretest_questions(): void
    {
        [
            $firstQuestion,
            $firstCorrectOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal pertama.'
        );

        $this->createQuestion(
            sequence: 2,
            questionText: 'Soal kedua.'
        );

        $questionsRoute = route(
            'student.missions.pretest.questions',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->from($questionsRoute)
            ->post(
                route(
                    'student.missions.pretest.submit',
                    [
                        'mission' => $this->mission->slug,
                    ]
                ),
                [
                    'answers' => [
                        $firstQuestion->id =>
                            $firstCorrectOption->id,
                    ],
                ]
            );

        $response->assertRedirect($questionsRoute);
        $response->assertSessionHasErrors('answers');

        $this->assertDatabaseHas(
            'pretest_attempts',
            [
                'id' => $this->pretestAttempt->id,
                'status' =>
                    PretestAttempt::STATUS_IN_PROGRESS,
            ]
        );

        $this->assertDatabaseCount(
            'pretest_answers',
            0
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
     * Pretest dengan beberapa soal dinilai dengan benar
     * dan menyimpan satu jawaban untuk setiap soal.
     */
    public function test_multiple_pretest_questions_are_scored_and_saved(): void
    {
        [
            $firstQuestion,
            $firstCorrectOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal pertama.'
        );

        [
            $secondQuestion,
            $secondCorrectOption,
            $secondWrongOption,
        ] = $this->createQuestion(
            sequence: 2,
            questionText: 'Soal kedua.'
        );

        [
            $thirdQuestion,
            $thirdCorrectOption,
        ] = $this->createQuestion(
            sequence: 3,
            questionText: 'Soal ketiga.'
        );

        $answers = [
            $firstQuestion->id =>
                $firstCorrectOption->id,
            $secondQuestion->id =>
                $secondWrongOption->id,
            $thirdQuestion->id =>
                $thirdCorrectOption->id,
        ];

        $submitRoute = route(
            'student.missions.pretest.submit',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $showRoute = route(
            'student.missions.pretest.show',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->post(
                $submitRoute,
                [
                    'answers' => $answers,
                ]
            );

        $response->assertRedirect($showRoute);

        $attempt = $this->pretestAttempt->fresh();

        $this->assertSame(
            PretestAttempt::STATUS_COMPLETED,
            $attempt->status
        );

        $this->assertSame(
            3,
            $attempt->total_questions
        );

        $this->assertSame(
            2,
            $attempt->correct_answers
        );

        $this->assertSame(
            '66.67',
            $attempt->score
        );

        $this->assertDatabaseCount(
            'pretest_answers',
            3
        );

        /*
         * Pretest tidak boleh langsung menyelesaikan misi
         * atau memberikan XP kepada siswa.
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
         * jawaban Pretest menjadi ganda.
         */
        $this
            ->actingAs($this->student)
            ->post(
                $submitRoute,
                [
                    'answers' => $answers,
                ]
            )
            ->assertRedirect($showRoute);

        $this->assertDatabaseCount(
            'pretest_answers',
            3
        );
    }

    /**
     * Membuat satu soal Pretest dengan
     * satu opsi benar dan satu opsi salah.
     *
     * @return array{
     *     0: PretestQuestion,
     *     1: PretestOption,
     *     2: PretestOption
     * }
     */
    private function createQuestion(
        int $sequence,
        string $questionText
    ): array {
        $question = PretestQuestion::create([
            'mission_id' => $this->mission->id,
            'question_text' => $questionText,
            'sequence' => $sequence,
            'is_active' => true,
        ]);

        $correctOption = PretestOption::create([
            'pretest_question_id' => $question->id,
            'option_text' => 'Jawaban benar',
            'sequence' => 1,
            'is_correct' => true,
        ]);

        $wrongOption = PretestOption::create([
            'pretest_question_id' => $question->id,
            'option_text' => 'Jawaban salah',
            'sequence' => 2,
            'is_correct' => false,
        ]);

        return [
            $question,
            $correctOption,
            $wrongOption,
        ];
    }
}
