<?php

namespace Tests\Feature\Student;

use App\Models\MicroExerciseAttempt;
use App\Models\MicroExerciseOption;
use App\Models\MicroExerciseQuestion;
use App\Models\Mission;
use App\Models\PretestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MicroExerciseTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Mission $mission;

    private int $materialBlockId;

    /**
     * Menyiapkan siswa, misi, Pretest selesai,
     * bagian materi, dan satu blok materi wajib.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $this->mission = Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'latihan-mikro-perulangan-test',
            'description' => 'Misi pengujian Latihan Mikro.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        PretestAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'status' => PretestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(15),
            'completed_at' => now()->subMinutes(10),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
        ]);

        $materialSectionId = DB::table(
            'material_sections'
        )->insertGetId([
            'mission_id' => $this->mission->id,
            'title' => 'Materi Perulangan',
            'slug' => 'materi-perulangan-test',
            'description' => 'Materi pengujian.',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->materialBlockId = DB::table(
            'material_blocks'
        )->insertGetId([
            'material_section_id' => $materialSectionId,
            'type' => 'paragraph',
            'title' => 'Pengertian Perulangan',
            'content' => 'Materi untuk pengujian Latihan Mikro.',
            'sequence' => 1,
            'minimum_read_seconds' => 0,
            'is_required' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    /**
 * Halaman Latihan Mikro tidak dapat dibuka
 * sebelum seluruh materi wajib diselesaikan.
 */
public function test_micro_exercise_page_is_rejected_before_required_material_is_completed(): void
{
    $response = $this
        ->actingAs($this->student)
        ->get(
            route(
                'student.missions.micro-exercise.show',
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
        'Selesaikan seluruh Materi Prakelas terlebih dahulu.'
    );
}

    /**
     * Request langsung menuju submit ditolak
     * jika materi wajib belum diselesaikan.
     */
    public function test_direct_submission_is_rejected_before_required_material_is_completed(): void
    {
        [
            $question,
            $correctOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal tentang perulangan.',
            points: 10
        );

        $response = $this
            ->actingAs($this->student)
            ->post(
                route(
                    'student.missions.micro-exercise.submit',
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
            'Selesaikan seluruh Materi Prakelas terlebih dahulu.'
        );

        $this->assertDatabaseMissing(
            'micro_exercise_attempts',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
            ]
        );

        $this->assertDatabaseCount(
            'micro_exercise_answers',
            0
        );
    }

    /**
     * Beberapa soal Latihan Mikro ditampilkan
     * berdasarkan urutan sequence.
     */
    public function test_micro_exercise_questions_are_displayed_in_sequence(): void
    {
        $this->completeRequiredMaterial();

        $this->createQuestion(
            sequence: 2,
            questionText: 'Pertanyaan kedua tentang perulangan.',
            points: 20
        );

        $this->createQuestion(
            sequence: 1,
            questionText: 'Pertanyaan pertama tentang perulangan.',
            points: 10
        );

        $response = $this
            ->actingAs($this->student)
            ->get(
                route(
                    'student.missions.micro-exercise.show',
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
     * sebelum menyelesaikan Latihan Mikro.
     */
    public function test_student_must_answer_all_micro_exercise_questions(): void
    {
        $this->completeRequiredMaterial();

        [
            $firstQuestion,
            $firstCorrectOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal pertama.',
            points: 10
        );

        $this->createQuestion(
            sequence: 2,
            questionText: 'Soal kedua.',
            points: 20
        );

        $showRoute = route(
            'student.missions.micro-exercise.show',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->from($showRoute)
            ->post(
                route(
                    'student.missions.micro-exercise.submit',
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
            'micro_exercise_attempts',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
            ]
        );

        $this->assertDatabaseCount(
            'micro_exercise_answers',
            0
        );
    }

    /**
     * Beberapa soal Latihan Mikro dinilai dengan benar
     * dan poin setiap jawaban benar dijumlahkan.
     */
    public function test_multiple_questions_are_scored_and_points_are_summed(): void
    {
        $this->completeRequiredMaterial();

        [
            $firstQuestion,
            $firstCorrectOption,
        ] = $this->createQuestion(
            sequence: 1,
            questionText: 'Soal pertama.',
            points: 10
        );

        [
            $secondQuestion,
            $secondCorrectOption,
            $secondWrongOption,
        ] = $this->createQuestion(
            sequence: 2,
            questionText: 'Soal kedua.',
            points: 20
        );

        [
            $thirdQuestion,
            $thirdCorrectOption,
        ] = $this->createQuestion(
            sequence: 3,
            questionText: 'Soal ketiga.',
            points: 30
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
            'student.missions.micro-exercise.submit',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $showRoute = route(
            'student.missions.micro-exercise.show',
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
            'Latihan Mikro berhasil diselesaikan.'
        );

        $attempt = MicroExerciseAttempt::query()
            ->where('user_id', $this->student->id)
            ->where('mission_id', $this->mission->id)
            ->firstOrFail();

        $this->assertSame(
            MicroExerciseAttempt::STATUS_COMPLETED,
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
         * Soal pertama benar memperoleh 10 poin.
         * Soal kedua salah memperoleh 0 poin.
         * Soal ketiga benar memperoleh 30 poin.
         */
        $this->assertSame(
            40,
            $attempt->earned_points
        );

        $this->assertDatabaseCount(
            'micro_exercise_answers',
            3
        );

        $this->assertDatabaseHas(
            'micro_exercise_answers',
            [
                'micro_exercise_attempt_id' =>
                    $attempt->id,
                'micro_exercise_question_id' =>
                    $firstQuestion->id,
                'is_correct' => true,
                'awarded_points' => 10,
            ]
        );

        $this->assertDatabaseHas(
            'micro_exercise_answers',
            [
                'micro_exercise_attempt_id' =>
                    $attempt->id,
                'micro_exercise_question_id' =>
                    $secondQuestion->id,
                'is_correct' => false,
                'awarded_points' => 0,
            ]
        );

        $this->assertDatabaseHas(
            'micro_exercise_answers',
            [
                'micro_exercise_attempt_id' =>
                    $attempt->id,
                'micro_exercise_question_id' =>
                    $thirdQuestion->id,
                'is_correct' => true,
                'awarded_points' => 30,
            ]
        );
        /*
 * Halaman hasil menampilkan umpan balik
 * untuk jawaban benar dan jawaban salah.
 */
$resultResponse = $this
    ->actingAs($this->student)
    ->get($showRoute);

$resultResponse->assertOk();

$resultResponse->assertSee(
    'Umpan Balik Jawaban'
);

$resultResponse->assertSee(
    'Jawaban Benar'
);

$resultResponse->assertSee(
    'Jawaban Belum Tepat'
);

$resultResponse->assertSee(
    'Jawaban benar.'
);

$resultResponse->assertSee(
    'Jawaban belum tepat.'
);

$resultResponse->assertSee(
    'Poin diperoleh:'
);

        /*
         * Pengiriman ulang tidak boleh membuat attempt
         * atau jawaban Latihan Mikro menjadi ganda.
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

        $attemptCount = MicroExerciseAttempt::query()
            ->where('user_id', $this->student->id)
            ->where('mission_id', $this->mission->id)
            ->count();

        $this->assertSame(
            1,
            $attemptCount
        );

        $this->assertDatabaseCount(
            'micro_exercise_answers',
            3
        );
    }

    /**
     * Menandai seluruh blok materi wajib
     * sebagai selesai untuk siswa.
     */
    private function completeRequiredMaterial(): void
    {
        DB::table('material_block_progresses')->insert([
            'user_id' => $this->student->id,
            'material_block_id' =>
                $this->materialBlockId,
            'first_opened_at' => now()->subMinutes(5),
            'last_activity_at' => now(),
            'total_read_seconds' => 300,
            'visit_count' => 1,
            'is_completed' => true,
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Membuat satu soal Latihan Mikro dengan
     * satu opsi benar dan satu opsi salah.
     *
     * @return array{
     *     0: MicroExerciseQuestion,
     *     1: MicroExerciseOption,
     *     2: MicroExerciseOption
     * }
     */
    private function createQuestion(
        int $sequence,
        string $questionText,
        int $points
    ): array {
        $question = MicroExerciseQuestion::create([
            'material_block_id' =>
                $this->materialBlockId,
            'type' =>
                MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text' => $questionText,
            'sequence' => $sequence,
            'points' => $points,
            'correct_feedback' => 'Jawaban benar.',
            'incorrect_feedback' => 'Jawaban belum tepat.',
            'is_active' => true,
        ]);

        $correctOption = MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
            'option_text' => 'Jawaban benar',
            'sequence' => 1,
            'is_correct' => true,
        ]);

        $wrongOption = MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
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
