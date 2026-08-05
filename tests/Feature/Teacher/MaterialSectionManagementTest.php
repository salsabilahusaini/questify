<?php

namespace Tests\Feature\Teacher;

use App\Models\MaterialBlock;
use App\Models\MaterialSection;
use App\Models\MicroExerciseAttempt;
use App\Models\MicroExerciseOption;
use App\Models\MicroExerciseQuestion;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MaterialSectionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat membuka formulir dan menambahkan
     * bagian materi baru pada sebuah misi.
     */
    public function test_teacher_can_create_material_section(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        MaterialSection::create([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' => 'pengertian-perulangan',
            'description' =>
                'Bagian materi pertama.',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.material-sections.create',
                    $mission
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.material-sections.create'
        );

        $createResponse->assertViewHas(
            'mission',
            $mission
        );

        $createResponse->assertViewHas(
            'nextSequence',
            2
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-sections.store',
                    $mission
                ),
                [
                    'title' =>
                        'Fungsi dan Ciri Perulangan',
                    'description' =>
                        'Mempelajari fungsi dan ciri struktur perulangan.',
                    'sequence' => 2,
                    'is_active' => '1',
                ]
            );

        $storeResponse->assertRedirect(
            route(
                'teacher.missions.show',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'success',
            'Bagian materi berhasil ditambahkan.'
        );

        $this->assertDatabaseHas(
            'material_sections',
            [
                'mission_id' => $mission->id,
                'title' =>
                    'Fungsi dan Ciri Perulangan',
                'slug' =>
                    'fungsi-dan-ciri-perulangan',
                'description' =>
                    'Mempelajari fungsi dan ciri struktur perulangan.',
                'sequence' => 2,
                'is_active' => true,
            ]
        );
    }

    /**
     * Slug bagian materi dibuat tetap unik
     * ketika judul yang sama digunakan kembali.
     */
    public function test_material_section_slug_is_generated_uniquely(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        MaterialSection::create([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' => 'pengertian-perulangan',
            'description' =>
                'Bagian materi pertama.',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-sections.store',
                    $mission
                ),
                [
                    'title' =>
                        'Pengertian Perulangan',
                    'description' =>
                        'Bagian lanjutan dengan judul yang sama.',
                    'sequence' => 2,
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect(
            route(
                'teacher.missions.show',
                $mission
            )
        );

        $this->assertDatabaseHas(
            'material_sections',
            [
                'mission_id' => $mission->id,
                'title' =>
                    'Pengertian Perulangan',
                'slug' =>
                    'pengertian-perulangan-2',
                'sequence' => 2,
            ]
        );

        $this->assertSame(
            2,
            MaterialSection::query()
                ->where('mission_id', $mission->id)
                ->count()
        );
    }

    /**
     * Urutan bagian yang sudah digunakan pada misi
     * yang sama harus ditolak oleh validasi.
     */
    public function test_material_section_sequence_must_be_unique_per_mission(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        MaterialSection::create([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' => 'pengertian-perulangan',
            'description' =>
                'Bagian materi pertama.',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $createRoute = route(
            'teacher.missions.material-sections.create',
            $mission
        );

        $response = $this
            ->actingAs($teacher)
            ->from($createRoute)
            ->post(
                route(
                    'teacher.missions.material-sections.store',
                    $mission
                ),
                [
                    'title' =>
                        'Bagian Materi Baru',
                    'description' =>
                        'Bagian dengan urutan yang sama.',
                    'sequence' => 1,
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect($createRoute);

        $response->assertSessionHasErrors(
            'sequence'
        );

        $this->assertDatabaseMissing(
            'material_sections',
            [
                'mission_id' => $mission->id,
                'title' => 'Bagian Materi Baru',
            ]
        );
    }

    /**
 * Guru dapat menghapus bagian materi yang belum
 * memiliki aktivitas peserta didik.
 */
public function test_teacher_can_delete_unused_material_section(): void
{
    $teacher = $this->createTeacher();

    $mission = $this->createMission();

    $section = MaterialSection::create([
        'mission_id' => $mission->id,
        'title' => 'Bagian Belum Digunakan',
        'slug' => 'bagian-belum-digunakan',
        'description' =>
            'Bagian yang belum dibuka peserta didik.',
        'sequence' => 1,
        'is_active' => true,
    ]);

    $block = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Blok Belum Digunakan',
        'content' =>
            'Materi yang belum dibaca peserta didik.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
    ]);

    $question = MicroExerciseQuestion::create([
        'material_block_id' => $block->id,
        'type' =>
            MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
        'question_text' =>
            'Soal yang belum pernah dijawab.',
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
            'option_text' => 'Pilihan benar',
            'sequence' => 1,
            'is_correct' => true,
        ]),
        MicroExerciseOption::create([
            'micro_exercise_question_id' =>
                $question->id,
            'option_text' => 'Pilihan salah',
            'sequence' => 2,
            'is_correct' => false,
        ]),
    ];

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.material-sections.destroy',
                [
                    'mission' => $mission,
                    'section' => $section,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.show',
            $mission
        )
    );

    $response->assertSessionHas(
        'success',
        'Bagian materi berhasil dihapus.'
    );

    $this->assertDatabaseMissing(
        'material_sections',
        [
            'id' => $section->id,
        ]
    );

    $this->assertDatabaseMissing(
        'material_blocks',
        [
            'id' => $block->id,
        ]
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
 * Bagian materi yang sudah memiliki riwayat
 * aktivitas peserta didik tidak boleh dihapus.
 */
public function test_teacher_cannot_delete_material_section_with_student_progress(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = MaterialSection::create([
        'mission_id' => $mission->id,
        'title' => 'Bagian yang Sudah Dibaca',
        'slug' => 'bagian-yang-sudah-dibaca',
        'description' =>
            'Bagian yang sudah dibuka peserta didik.',
        'sequence' => 1,
        'is_active' => true,
    ]);

    $block = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Blok yang Sudah Dibaca',
        'content' =>
            'Materi yang sudah dibaca peserta didik.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 20,
        'is_required' => true,
        'is_active' => true,
    ]);

    $progressId = DB::table(
        'material_block_progresses'
    )->insertGetId([
        'user_id' => $student->id,
        'material_block_id' => $block->id,
        'first_opened_at' =>
            now()->subMinutes(10),
        'last_activity_at' => now(),
        'total_read_seconds' => 120,
        'visit_count' => 2,
        'is_completed' => true,
        'completed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.material-sections.destroy',
                [
                    'mission' => $mission,
                    'section' => $section,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.show',
            $mission
        )
    );

    $response->assertSessionHas(
        'error',
        'Bagian materi tidak dapat dihapus karena sudah memiliki riwayat aktivitas peserta didik. Nonaktifkan bagian melalui menu Edit Bagian.'
    );

    $this->assertDatabaseHas(
        'material_sections',
        [
            'id' => $section->id,
        ]
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $block->id,
            'material_section_id' => $section->id,
        ]
    );

    $this->assertDatabaseHas(
        'material_block_progresses',
        [
            'id' => $progressId,
            'user_id' => $student->id,
            'material_block_id' => $block->id,
            'total_read_seconds' => 120,
            'visit_count' => 2,
            'is_completed' => true,
        ]
    );
}
/**
 * Bagian materi yang Latihan Mikronya sudah memiliki
 * jawaban peserta didik tidak boleh dihapus.
 */
public function test_teacher_cannot_delete_material_section_with_micro_exercise_answers(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = MaterialSection::create([
        'mission_id' => $mission->id,
        'title' =>
            'Bagian dengan Jawaban Latihan Mikro',
        'slug' =>
            'bagian-dengan-jawaban-latihan-mikro',
        'description' =>
            'Bagian yang memiliki jawaban peserta didik.',
        'sequence' => 1,
        'is_active' => true,
    ]);

    $block = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' =>
            'Blok dengan Jawaban Latihan Mikro',
        'content' =>
            'Materi yang memiliki jawaban peserta didik.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 20,
        'is_required' => true,
        'is_active' => true,
    ]);

    $question = MicroExerciseQuestion::create([
        'material_block_id' => $block->id,
        'type' =>
            MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
        'question_text' =>
            'Soal yang sudah dijawab peserta didik.',
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
                'teacher.missions.material-sections.destroy',
                [
                    'mission' => $mission,
                    'section' => $section,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.show',
            $mission
        )
    );

    $response->assertSessionHas(
        'error',
        'Bagian materi tidak dapat dihapus karena Latihan Mikro di dalamnya sudah memiliki jawaban peserta didik. Nonaktifkan bagian melalui menu Edit Bagian.'
    );

    $this->assertDatabaseHas(
        'material_sections',
        [
            'id' => $section->id,
        ]
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $block->id,
            'material_section_id' => $section->id,
        ]
    );

    $this->assertDatabaseHas(
        'micro_exercise_questions',
        [
            'id' => $question->id,
            'material_block_id' => $block->id,
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
                'kelola-bagian-materi-test',
            'description' =>
                'Misi pengujian pengelolaan bagian materi.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }
}
