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

class MaterialBlockManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat membuka formulir dan menambahkan
     * blok materi baru pada sebuah bagian.
     */
    public function test_teacher_can_create_material_block(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $section = $this->createSection(
            $mission
        );

        MaterialBlock::create([
            'material_section_id' => $section->id,
            'type' => MaterialBlock::TYPE_HEADING,
            'title' => 'Apa Itu Perulangan?',
            'content' => null,
            'code_language' => null,
            'media_path' => null,
            'sequence' => 1,
            'minimum_read_seconds' => 0,
            'is_required' => true,
            'is_active' => true,
        ]);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.material-blocks.create',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.material-blocks.create'
        );

        $createResponse->assertViewHas(
            'mission',
            $mission
        );

        $createResponse->assertViewHas(
            'section',
            $section
        );

        $createResponse->assertViewHas(
            'nextSequence',
            2
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-blocks.store',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ),
                [
                    'type' =>
                        MaterialBlock::TYPE_PARAGRAPH,
                    'title' =>
                        'Fungsi Perulangan',
                    'content' =>
                        'Perulangan digunakan untuk menjalankan perintah secara berulang.',
                    'code_language' => 'php',
                    'media_path' =>
                        'materials/gambar-yang-tidak-digunakan.png',
                    'sequence' => 2,
                    'minimum_read_seconds' => 20,
                    'is_required' => '1',
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
            'Blok materi berhasil ditambahkan.'
        );

        $this->assertDatabaseHas(
            'material_blocks',
            [
                'material_section_id' =>
                    $section->id,
                'type' =>
                    MaterialBlock::TYPE_PARAGRAPH,
                'title' =>
                    'Fungsi Perulangan',
                'content' =>
                    'Perulangan digunakan untuk menjalankan perintah secara berulang.',
                'code_language' => null,
                'media_path' => null,
                'sequence' => 2,
                'minimum_read_seconds' => 20,
                'is_required' => true,
                'is_active' => true,
            ]
        );
    }

    /**
     * Bahasa kode disimpan hanya untuk blok
     * yang berjenis Kode Program.
     */
    public function test_code_block_stores_code_language(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $section = $this->createSection(
            $mission
        );

        $response = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-blocks.store',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ),
                [
                    'type' =>
                        MaterialBlock::TYPE_CODE,
                    'title' =>
                        'Contoh Perulangan Sederhana',
                    'content' => <<<'CODE'
for ($i = 1; $i <= 5; $i++) {
    echo $i;
}
CODE,
                    'code_language' => 'php',
                    'media_path' =>
                        'materials/gambar-yang-tidak-digunakan.png',
                    'sequence' => 1,
                    'minimum_read_seconds' => 15,
                    'is_required' => '1',
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
            'material_blocks',
            [
                'material_section_id' =>
                    $section->id,
                'type' =>
                    MaterialBlock::TYPE_CODE,
                'title' =>
                    'Contoh Perulangan Sederhana',
                'code_language' => 'php',
                'media_path' => null,
                'sequence' => 1,
                'minimum_read_seconds' => 15,
                'is_required' => true,
                'is_active' => true,
            ]
        );
    }

    /**
     * Urutan blok yang sudah digunakan dalam bagian
     * yang sama harus ditolak oleh validasi.
     */
    public function test_material_block_sequence_must_be_unique_per_section(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $section = $this->createSection(
            $mission
        );

        MaterialBlock::create([
            'material_section_id' => $section->id,
            'type' =>
                MaterialBlock::TYPE_PARAGRAPH,
            'title' =>
                'Blok Materi Pertama',
            'content' =>
                'Isi blok materi pertama.',
            'code_language' => null,
            'media_path' => null,
            'sequence' => 1,
            'minimum_read_seconds' => 10,
            'is_required' => true,
            'is_active' => true,
        ]);

        $createRoute = route(
            'teacher.missions.material-blocks.create',
            [
                'mission' => $mission,
                'section' => $section,
            ]
        );

        $response = $this
            ->actingAs($teacher)
            ->from($createRoute)
            ->post(
                route(
                    'teacher.missions.material-blocks.store',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ),
                [
                    'type' =>
                        MaterialBlock::TYPE_PARAGRAPH,
                    'title' =>
                        'Blok Materi Kedua',
                    'content' =>
                        'Isi blok dengan urutan yang sama.',
                    'code_language' => null,
                    'media_path' => null,
                    'sequence' => 1,
                    'minimum_read_seconds' => 10,
                    'is_required' => '1',
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect(
            $createRoute
        );

        $response->assertSessionHasErrors(
            'sequence'
        );

        $this->assertDatabaseMissing(
            'material_blocks',
            [
                'material_section_id' =>
                    $section->id,
                'title' =>
                    'Blok Materi Kedua',
            ]
        );
    }
    /**
 * Urutan blok yang sudah digunakan dalam bagian
 * yang sama tidak boleh digunakan saat edit.
 */
public function test_material_block_sequence_must_remain_unique_when_updated(): void
{
    $teacher = $this->createTeacher();

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

    MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' => MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Blok Pertama',
        'content' => 'Isi blok pertama.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
    ]);

    $secondBlock = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' => MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Blok Kedua',
        'content' => 'Isi blok kedua.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 2,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
    ]);

    $editRoute = route(
        'teacher.missions.material-blocks.edit',
        [
            'mission' => $mission,
            'block' => $secondBlock,
        ]
    );

    $response = $this
        ->actingAs($teacher)
        ->from($editRoute)
        ->put(
            route(
                'teacher.missions.material-blocks.update',
                [
                    'mission' => $mission,
                    'block' => $secondBlock,
                ]
            ),
            [
                'type' => MaterialBlock::TYPE_PARAGRAPH,
                'title' => 'Blok Kedua',
                'content' => 'Isi blok kedua.',
                'code_language' => null,
                'media_path' => null,
                'sequence' => 1,
                'minimum_read_seconds' => 10,
                'is_required' => '1',
                'is_active' => '1',
            ]
        );

    $response->assertRedirect($editRoute);

    $response->assertSessionHasErrors(
        'sequence'
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $secondBlock->id,
            'sequence' => 2,
        ]
    );
}
/**
 * Guru tidak dapat menambahkan blok aktif dan wajib
 * setelah materi sudah memiliki aktivitas siswa.
 */
public function test_teacher_cannot_add_required_active_block_after_student_material_activity(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

    $existingBlock = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' =>
            'Blok Materi Awal',
        'content' =>
            'Materi yang sudah mulai dipelajari siswa.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
    ]);

    DB::table(
        'material_block_progresses'
    )->insert([
        'user_id' => $student->id,
        'material_block_id' =>
            $existingBlock->id,
        'first_opened_at' => now(),
        'last_activity_at' => now(),
        'total_read_seconds' => 10,
        'visit_count' => 1,
        'is_completed' => true,
        'completed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $createRoute = route(
        'teacher.missions.material-blocks.create',
        [
            'mission' => $mission,
            'section' => $section,
        ]
    );

    $response = $this
        ->actingAs($teacher)
        ->from($createRoute)
        ->post(
            route(
                'teacher.missions.material-blocks.store',
                [
                    'mission' => $mission,
                    'section' => $section,
                ]
            ),
            [
                'type' =>
                    MaterialBlock::TYPE_CHECKPOINT,
                'title' =>
                    'Checkpoint Baru',
                'content' =>
                    'Checkpoint yang ditambahkan setelah siswa belajar.',
                'code_language' => null,
                'media_path' => null,
                'sequence' => 2,
                'minimum_read_seconds' => 0,
                'is_required' => '1',
                'is_active' => '1',
            ]
        );

    $response->assertRedirect(
        $createRoute
    );

    $response->assertSessionHas(
        'error',
        'Blok aktif dan wajib tidak dapat ditambahkan karena materi sudah memiliki aktivitas peserta didik.'
    );

    $this->assertDatabaseMissing(
        'material_blocks',
        [
            'material_section_id' =>
                $section->id,
            'title' =>
                'Checkpoint Baru',
        ]
    );
}
/**
 * Guru tidak dapat menjadikan blok aktif dan wajib
 * setelah materi sudah memiliki aktivitas siswa.
 */
public function test_teacher_cannot_make_block_required_and_active_after_student_material_activity(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

    $trackedBlock = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Blok yang Sudah Dibaca',
        'content' =>
            'Materi yang sudah dipelajari siswa.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
    ]);

    $optionalBlock = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Blok Opsional',
        'content' =>
            'Blok yang semula tidak wajib.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 2,
        'minimum_read_seconds' => 10,
        'is_required' => false,
        'is_active' => true,
    ]);

    DB::table(
        'material_block_progresses'
    )->insert([
        'user_id' => $student->id,
        'material_block_id' =>
            $trackedBlock->id,
        'first_opened_at' => now(),
        'last_activity_at' => now(),
        'total_read_seconds' => 10,
        'visit_count' => 1,
        'is_completed' => true,
        'completed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $editRoute = route(
        'teacher.missions.material-blocks.edit',
        [
            'mission' => $mission,
            'block' => $optionalBlock,
        ]
    );

    $response = $this
        ->actingAs($teacher)
        ->from($editRoute)
        ->put(
            route(
                'teacher.missions.material-blocks.update',
                [
                    'mission' => $mission,
                    'block' => $optionalBlock,
                ]
            ),
            [
                'type' =>
                    MaterialBlock::TYPE_PARAGRAPH,
                'title' => 'Blok Opsional',
                'content' =>
                    'Blok yang semula tidak wajib.',
                'code_language' => null,
                'media_path' => null,
                'sequence' => 2,
                'minimum_read_seconds' => 10,
                'is_required' => '1',
                'is_active' => '1',
            ]
        );

    $response->assertRedirect(
        $editRoute
    );

    $response->assertSessionHas(
        'error',
        'Blok tidak dapat dijadikan aktif dan wajib karena materi sudah memiliki aktivitas peserta didik.'
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $optionalBlock->id,
            'is_required' => false,
            'is_active' => true,
        ]
    );
}
/**
 * Blok aktif dan wajib tidak boleh diubah
 * setelah materi memiliki aktivitas peserta didik.
 */
public function test_teacher_cannot_change_required_active_block_after_student_material_activity(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

    $block = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' => 'Materi yang Sudah Digunakan',
        'content' =>
            'Isi materi sebelum aktivitas siswa.',
        'code_language' => null,
        'media_path' => null,
        'sequence' => 1,
        'minimum_read_seconds' => 20,
        'is_required' => true,
        'is_active' => true,
    ]);

    DB::table(
        'material_block_progresses'
    )->insert([
        'user_id' => $student->id,
        'material_block_id' => $block->id,
        'first_opened_at' =>
            now()->subMinutes(5),
        'last_activity_at' => now(),
        'total_read_seconds' => 20,
        'visit_count' => 1,
        'is_completed' => true,
        'completed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $editRoute = route(
        'teacher.missions.material-blocks.edit',
        [
            'mission' => $mission,
            'block' => $block,
        ]
    );

    $response = $this
        ->actingAs($teacher)
        ->from($editRoute)
        ->put(
            route(
                'teacher.missions.material-blocks.update',
                [
                    'mission' => $mission,
                    'block' => $block,
                ]
            ),
            [
                'type' =>
                    MaterialBlock::TYPE_PARAGRAPH,
                'title' =>
                    'Materi yang Sudah Digunakan',
                'content' =>
                    'Isi materi setelah diubah guru.',
                'code_language' => null,
                'media_path' => null,
                'sequence' => 1,
                'minimum_read_seconds' => 60,
                'is_required' => '1',
                'is_active' => '1',
            ]
        );

    $response->assertRedirect(
        $editRoute
    );

    $response->assertSessionHas(
        'error',
        'Blok aktif dan wajib tidak dapat diubah karena materi sudah memiliki aktivitas peserta didik.'
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $block->id,
            'content' =>
                'Isi materi sebelum aktivitas siswa.',
            'minimum_read_seconds' => 20,
            'is_required' => true,
            'is_active' => true,
        ]
    );
}
    /**
 * Guru dapat menghapus blok materi yang belum
 * memiliki aktivitas peserta didik.
 */
public function test_teacher_can_delete_unused_material_block(): void
{
    $teacher = $this->createTeacher();

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

    $block = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' =>
            'Blok yang Belum Digunakan',
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
                'teacher.missions.material-blocks.destroy',
                [
                    'mission' => $mission,
                    'block' => $block,
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
        'Blok materi berhasil dihapus.'
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
 * Blok materi yang sudah memiliki riwayat aktivitas
 * peserta didik tidak boleh dihapus.
 */
public function test_teacher_cannot_delete_material_block_with_student_progress(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

    $block = MaterialBlock::create([
        'material_section_id' => $section->id,
        'type' =>
            MaterialBlock::TYPE_PARAGRAPH,
        'title' =>
            'Blok yang Sudah Dibaca',
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
                'teacher.missions.material-blocks.destroy',
                [
                    'mission' => $mission,
                    'block' => $block,
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
        'Blok materi tidak dapat dihapus karena sudah memiliki riwayat aktivitas peserta didik. Nonaktifkan blok melalui menu Edit Blok.'
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $block->id,
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
 * Blok materi yang Latihan Mikronya sudah memiliki
 * jawaban peserta didik tidak boleh dihapus.
 */
public function test_teacher_cannot_delete_material_block_with_micro_exercise_answers(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $section = $this->createSection(
        $mission
    );

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
                'teacher.missions.material-blocks.destroy',
                [
                    'mission' => $mission,
                    'block' => $block,
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
        'Blok materi tidak dapat dihapus karena Latihan Mikro di dalamnya sudah memiliki jawaban peserta didik. Nonaktifkan blok melalui menu Edit Blok.'
    );

    $this->assertDatabaseHas(
        'material_blocks',
        [
            'id' => $block->id,
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
                'kelola-blok-materi-test',
            'description' =>
                'Misi pengujian pengelolaan blok materi.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu bagian materi pada misi.
     */
    private function createSection(
        Mission $mission
    ): MaterialSection {
        return MaterialSection::create([
            'mission_id' => $mission->id,
            'title' =>
                'Pengertian Perulangan',
            'slug' =>
                'pengertian-perulangan',
            'description' =>
                'Bagian materi untuk pengujian blok.',
            'sequence' => 1,
            'is_active' => true,
        ]);
    }
}
