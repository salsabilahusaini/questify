<?php

namespace Tests\Feature\Teacher;

use App\Models\MicroExerciseOption;
use App\Models\MicroExerciseAttempt;
use App\Models\MicroExerciseQuestion;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MicroExerciseQuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat membuka formulir dan menambahkan
     * soal Latihan Mikro dengan empat pilihan.
     */
    public function test_teacher_can_create_micro_exercise_question(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $materialBlockId = $this->createMaterialBlock(
            $mission
        );

        MicroExerciseQuestion::create([
            'material_block_id' =>
                $materialBlockId,
            'type' =>
                MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text' =>
                'Soal Latihan Mikro sebelumnya.',
            'sequence' => 1,
            'points' => 10,
            'correct_feedback' =>
                'Jawaban benar.',
            'incorrect_feedback' =>
                'Jawaban belum tepat.',
            'is_active' => true,
        ]);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.micro-exercises.create',
                    $mission
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.micro-exercises.create'
        );

        $createResponse->assertViewHas(
            'mission',
            $mission
        );

        $createResponse->assertViewHas(
            'materialBlocks',
            function ($materialBlocks) use (
                $materialBlockId
            ): bool {
                return $materialBlocks
                    ->pluck('id')
                    ->contains($materialBlockId);
            }
        );

        $createResponse->assertViewHas(
            'nextSequenceByBlock',
            function ($nextSequenceByBlock) use (
                $materialBlockId
            ): bool {
                return (int) $nextSequenceByBlock
                    ->get($materialBlockId) === 2;
            }
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.micro-exercises.store',
                    $mission
                ),
                [
                    'material_block_id' =>
                        $materialBlockId,
                    'question_text' =>
                        'Struktur manakah yang digunakan untuk menjalankan perintah secara berulang?',
                    'sequence' => 2,
                    'points' => 10,
                    'correct_feedback' =>
                        'Tepat. Struktur perulangan menjalankan perintah secara berulang.',
                    'incorrect_feedback' =>
                        'Belum tepat. Perhatikan fungsi struktur perulangan.',
                    'is_active' => '1',
                    'correct_option_index' => 1,
                    'options' => [
                        [
                            'option_text' =>
                                'Struktur percabangan',
                            'sequence' => 1,
                        ],
                        [
                            'option_text' =>
                                'Struktur perulangan',
                            'sequence' => 2,
                        ],
                        [
                            'option_text' =>
                                'Deklarasi variabel',
                            'sequence' => 3,
                        ],
                        [
                            'option_text' =>
                                'Pemanggilan fungsi',
                            'sequence' => 4,
                        ],
                    ],
                ]
            );

        $storeResponse->assertRedirect(
            route(
                'teacher.missions.micro-exercises.index',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'success',
            'Soal Latihan Mikro berhasil ditambahkan.'
        );

        $newQuestion = MicroExerciseQuestion::query()
            ->where(
                'material_block_id',
                $materialBlockId
            )
            ->where('sequence', 2)
            ->first();

        $this->assertNotNull($newQuestion);

        $this->assertDatabaseHas(
            'micro_exercise_questions',
            [
                'id' => $newQuestion->id,
                'material_block_id' =>
                    $materialBlockId,
                'type' =>
                    MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
                'question_text' =>
                    'Struktur manakah yang digunakan untuk menjalankan perintah secara berulang?',
                'sequence' => 2,
                'points' => 10,
                'correct_feedback' =>
                    'Tepat. Struktur perulangan menjalankan perintah secara berulang.',
                'incorrect_feedback' =>
                    'Belum tepat. Perhatikan fungsi struktur perulangan.',
                'is_active' => true,
            ]
        );

        $this->assertSame(
            4,
            MicroExerciseOption::query()
                ->where(
                    'micro_exercise_question_id',
                    $newQuestion->id
                )
                ->count()
        );

        $this->assertDatabaseHas(
            'micro_exercise_options',
            [
                'micro_exercise_question_id' =>
                    $newQuestion->id,
                'option_text' =>
                    'Struktur perulangan',
                'sequence' => 2,
                'is_correct' => true,
            ]
        );

        $this->assertDatabaseHas(
            'micro_exercise_options',
            [
                'micro_exercise_question_id' =>
                    $newQuestion->id,
                'option_text' =>
                    'Struktur percabangan',
                'sequence' => 1,
                'is_correct' => false,
            ]
        );
    }
    /**
 * Guru dapat menghapus soal Latihan Mikro
 * yang belum pernah dijawab peserta didik.
 */
public function test_teacher_can_delete_unused_micro_exercise_question(): void
{
    $teacher = $this->createTeacher();

    $mission = $this->createMission();

    $materialBlockId = $this->createMaterialBlock(
        $mission
    );

    $question = MicroExerciseQuestion::create([
        'material_block_id' =>
            $materialBlockId,
        'type' =>
            MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
        'question_text' =>
            'Soal Latihan Mikro yang belum dijawab.',
        'sequence' => 1,
        'points' => 10,
        'correct_feedback' =>
            'Jawaban benar.',
        'incorrect_feedback' =>
            'Jawaban belum tepat.',
        'is_active' => true,
    ]);

    $options = [
        MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
            'option_text' => 'Pilihan A',
            'sequence' => 1,
            'is_correct' => true,
        ]),
        MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
            'option_text' => 'Pilihan B',
            'sequence' => 2,
            'is_correct' => false,
        ]),
        MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
            'option_text' => 'Pilihan C',
            'sequence' => 3,
            'is_correct' => false,
        ]),
        MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
            'option_text' => 'Pilihan D',
            'sequence' => 4,
            'is_correct' => false,
        ]),
    ];

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.micro-exercises.destroy',
                [
                    'mission' => $mission,
                    'question' => $question,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.micro-exercises.index',
            $mission
        )
    );

    $response->assertSessionHas(
        'success',
        'Soal Latihan Mikro berhasil dihapus.'
    );

    $this->assertDatabaseMissing(
        'micro_exercise_questions',
        [
            'id' => $question->id,
        ]
    );

    foreach ($options as $option) {
        $this->assertDatabaseMissing(
            'micro_exercise_options',
            [
                'id' => $option->id,
            ]
        );
    }
}
/**
 * Soal Latihan Mikro yang sudah memiliki jawaban
 * peserta didik tidak boleh dihapus.
 */
public function test_teacher_cannot_delete_answered_micro_exercise_question(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $materialBlockId = $this->createMaterialBlock(
        $mission
    );

    $question = MicroExerciseQuestion::create([
        'material_block_id' =>
            $materialBlockId,
        'type' =>
            MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
        'question_text' =>
            'Soal Latihan Mikro yang sudah dijawab.',
        'sequence' => 1,
        'points' => 10,
        'correct_feedback' =>
            'Jawaban benar.',
        'incorrect_feedback' =>
            'Jawaban belum tepat.',
        'is_active' => true,
    ]);

    $correctOption = MicroExerciseOption::create([
        'micro_exercise_question_id' =>
            $question->id,
        'option_text' => 'Pilihan benar',
        'sequence' => 1,
        'is_correct' => true,
    ]);

    MicroExerciseOption::create([
        'micro_exercise_question_id' =>
            $question->id,
        'option_text' => 'Pilihan salah',
        'sequence' => 2,
        'is_correct' => false,
    ]);

    $attempt = MicroExerciseAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            MicroExerciseAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(5),
        'completed_at' => now(),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
        'earned_points' => 10,
    ]);

    DB::table('micro_exercise_answers')->insert([
        'micro_exercise_attempt_id' =>
            $attempt->id,
        'micro_exercise_question_id' =>
            $question->id,
        'selected_option_id' =>
            $correctOption->id,
        'is_correct' => true,
        'awarded_points' => 10,
        'answered_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.micro-exercises.destroy',
                [
                    'mission' => $mission,
                    'question' => $question,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.micro-exercises.index',
            $mission
        )
    );

    $response->assertSessionHas(
        'error',
        'Soal Latihan Mikro tidak dapat dihapus karena sudah memiliki jawaban peserta didik. Nonaktifkan soal melalui menu Edit Soal.'
    );

    $this->assertDatabaseHas(
        'micro_exercise_questions',
        [
            'id' => $question->id,
        ]
    );

    $this->assertDatabaseHas(
        'micro_exercise_attempts',
        [
            'id' => $attempt->id,
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'earned_points' => 10,
        ]
    );

    $this->assertDatabaseHas(
        'micro_exercise_answers',
        [
            'micro_exercise_attempt_id' =>
                $attempt->id,
            'micro_exercise_question_id' =>
                $question->id,
            'selected_option_id' =>
                $correctOption->id,
            'is_correct' => true,
            'awarded_points' => 10,
        ]
    );
}
    /**
     * Membuat akun guru untuk pengujian.
     */
    private function createTeacher(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);
    }

    /**
     * Membuat satu misi yang dipublikasikan.
     */
    private function createMission(): Mission
    {
        return Mission::create([
            'title' =>
                'Memahami Konsep Perulangan',
            'slug' =>
                'kelola-latihan-mikro-test',
            'description' =>
                'Misi pengujian pengelolaan Latihan Mikro.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat bagian dan satu blok materi
     * yang dimiliki oleh misi.
     */
    private function createMaterialBlock(
        Mission $mission
    ): int {
        $materialSectionId = DB::table(
            'material_sections'
        )->insertGetId([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' =>
                'pengertian-perulangan-test',
            'description' =>
                'Bagian materi untuk pengujian.',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table(
            'material_blocks'
        )->insertGetId([
            'material_section_id' =>
                $materialSectionId,
            'type' => 'paragraph',
            'title' => 'Apa Itu Perulangan?',
            'content' =>
                'Materi pengujian Latihan Mikro.',
            'sequence' => 1,
            'minimum_read_seconds' => 0,
            'is_required' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
