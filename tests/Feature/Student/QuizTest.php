<?php

namespace Tests\Feature\Student;

use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Mission $mission;

    /**
     * Menyiapkan siswa dan misi
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
            'slug' => 'kuis-materi-perulangan-test',
            'description' => 'Misi pengujian Kuis Materi.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }
    /**
 * Halaman Kuis Materi tidak dapat dibuka
 * sebelum Latihan Mikro diselesaikan.
 */
public function test_quiz_page_is_rejected_before_micro_exercise_is_completed(): void
{
    $response = $this
        ->actingAs($this->student)
        ->get(
            route(
                'student.missions.quiz.show',
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
        'Selesaikan Latihan Mikro terlebih dahulu.'
    );
}

    /**
     * Request langsung menuju submit ditolak
     * jika Latihan Mikro belum diselesaikan.
     */
    public function test_direct_submission_is_rejected_before_micro_exercise_is_completed(): void
    {
        [
            $question,
            $correctOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal tentang perulangan.',
            points: 20
        );

        $response = $this
            ->actingAs($this->student)
            ->post(
                route(
                    'student.missions.quiz.submit',
                    [
                        'mission' => $this->mission->slug,
                    ]
                ),
                [
                    'answers' => [
                        $question->id =>
                            $correctOption->id,
                    ],
                ]
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
            'Selesaikan Latihan Mikro terlebih dahulu.'
        );

        $this->assertDatabaseMissing(
            'quiz_attempts',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
            ]
        );

        $this->assertDatabaseCount(
            'quiz_answers',
            0
        );
    }

    /**
     * Beberapa soal Kuis Materi ditampilkan
     * berdasarkan urutan sequence.
     */
    public function test_quiz_questions_are_displayed_in_sequence(): void
    {
        $this->completeMicroExercise();

        $this->createQuestion(
            sequence: 2,
            questionText: 'Pertanyaan kedua tentang perulangan.',
            points: 30
        );

        $this->createQuestion(
            sequence: 1,
            questionText: 'Pertanyaan pertama tentang perulangan.',
            points: 20
        );

        $response = $this
            ->actingAs($this->student)
            ->get(
                route(
                    'student.missions.quiz.show',
                    [
                        'mission' => $this->mission->slug,
                    ]
                )
            );

        $response->assertOk();

        $response->assertSeeInOrder([
            'Pertanyaan pertama tentang perulangan.',
            'Pertanyaan kedua tentang perulangan.',
        ]);
    }

    /**
     * Siswa wajib menjawab seluruh soal
     * sebelum menyelesaikan Kuis Materi.
     */
    public function test_student_must_answer_all_quiz_questions(): void
    {
        $this->completeMicroExercise();

        [
            $firstQuestion,
            $firstCorrectOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal pertama.',
            points: 20
        );

        $this->createQuestion(
            sequence: 2,
            questionText: 'Soal kedua.',
            points: 30
        );

        $showRoute = route(
            'student.missions.quiz.show',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->from($showRoute)
            ->post(
                route(
                    'student.missions.quiz.submit',
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

        $response->assertRedirect($showRoute);
        $response->assertSessionHasErrors('answers');

        $this->assertDatabaseMissing(
            'quiz_attempts',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
            ]
        );

        $this->assertDatabaseCount(
            'quiz_answers',
            0
        );
    }
    /**
 * Opsi milik soal lain tidak boleh digunakan
 * sebagai jawaban untuk suatu soal.
 */
public function test_option_from_another_question_is_rejected(): void
{
    $this->completeMicroExercise();

    [
        $firstQuestion,
        $firstCorrectOption,
    ] = $this->createQuestion(
        sequence: 1,
        questionText: 'Soal pertama.',
        points: 20
    );

    [
        $secondQuestion,
        $secondCorrectOption,
    ] = $this->createQuestion(
        sequence: 2,
        questionText: 'Soal kedua.',
        points: 30
    );

    $showRoute = route(
        'student.missions.quiz.show',
        [
            'mission' => $this->mission->slug,
        ]
    );

    $response = $this
        ->actingAs($this->student)
        ->from($showRoute)
        ->post(
            route(
                'student.missions.quiz.submit',
                [
                    'mission' => $this->mission->slug,
                ]
            ),
            [
                'answers' => [
                    $firstQuestion->id =>
                        $secondCorrectOption->id,

                    $secondQuestion->id =>
                        $firstCorrectOption->id,
                ],
            ]
        );

    $response->assertRedirect($showRoute);
    $response->assertSessionHasErrors('answers');

    $this->assertDatabaseMissing(
        'quiz_attempts',
        [
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
        ]
    );

    $this->assertDatabaseCount(
        'quiz_answers',
        0
    );
}

    /**
     * Beberapa soal Kuis Materi dinilai dengan benar
     * dan poin setiap jawaban benar dijumlahkan.
     */
    public function test_multiple_questions_are_scored_and_points_are_summed(): void
    {
        $this->completeMicroExercise();

        [
            $firstQuestion,
            $firstCorrectOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal pertama.',
            points: 20
        );

        [
            $secondQuestion,
            $secondCorrectOption,
            $secondWrongOption,
        ] = $this->createQuestion(
            sequence: 2,
            questionText: 'Soal kedua.',
            points: 30
        );

        [
            $thirdQuestion,
            $thirdCorrectOption,
        ] = $this->createQuestion(
            sequence: 3,
            questionText: 'Soal ketiga.',
            points: 50
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
            'student.missions.quiz.submit',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $showRoute = route(
            'student.missions.quiz.show',
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

        $response->assertSessionHas(
            'success',
            'Kuis Materi berhasil diselesaikan.'
        );

        $attempt = QuizAttempt::query()
            ->where('user_id', $this->student->id)
            ->where('mission_id', $this->mission->id)
            ->firstOrFail();

        $this->assertSame(
            QuizAttempt::STATUS_COMPLETED,
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

        /*
         * Soal pertama benar memperoleh 20 poin.
         * Soal kedua salah memperoleh 0 poin.
         * Soal ketiga benar memperoleh 50 poin.
         */
        $this->assertSame(
            70,
            $attempt->earned_points
        );

        $this->assertDatabaseCount(
            'quiz_answers',
            3
        );

        $this->assertDatabaseHas(
            'quiz_answers',
            [
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' =>
                    $firstQuestion->id,
                'is_correct' => true,
                'awarded_points' => 20,
            ]
        );

        $this->assertDatabaseHas(
            'quiz_answers',
            [
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' =>
                    $secondQuestion->id,
                'is_correct' => false,
                'awarded_points' => 0,
            ]
        );

        $this->assertDatabaseHas(
            'quiz_answers',
            [
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' =>
                    $thirdQuestion->id,
                'is_correct' => true,
                'awarded_points' => 50,
            ]
        );

        /*
         * Pengiriman ulang tidak boleh membuat attempt
         * atau jawaban Kuis Materi menjadi ganda.
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

        $attemptCount = QuizAttempt::query()
            ->where('user_id', $this->student->id)
            ->where('mission_id', $this->mission->id)
            ->count();

        $this->assertSame(
            1,
            $attemptCount
        );

        $this->assertDatabaseCount(
            'quiz_answers',
            3
        );
    }

    /**
     * Membuat Latihan Mikro siswa
     * berstatus selesai.
     */
    private function completeMicroExercise(): void
    {
        MicroExerciseAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'status' =>
                MicroExerciseAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
            'earned_points' => 10,
        ]);
    }

    /**
     * Membuat satu soal Kuis Materi dengan
     * satu opsi benar dan satu opsi salah.
     *
     * @return array{
     *     0: QuizQuestion,
     *     1: QuizOption,
     *     2: QuizOption
     * }
     */
    private function createQuestion(
        int $sequence,
        string $questionText,
        int $points
    ): array {
        $question = QuizQuestion::create([
            'mission_id' => $this->mission->id,
            'type' =>
                QuizQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text' => $questionText,
            'sequence' => $sequence,
            'points' => $points,
            'explanation' =>
                'Penjelasan jawaban Kuis Materi.',
            'is_active' => true,
        ]);

        $correctOption = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Jawaban benar',
            'sequence' => 1,
            'is_correct' => true,
        ]);

        $wrongOption = QuizOption::create([
            'quiz_question_id' => $question->id,
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
