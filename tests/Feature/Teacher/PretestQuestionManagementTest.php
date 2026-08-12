<?php

namespace Tests\Feature\Teacher;

use App\Models\Mission;
use App\Models\PretestAnswer;
use App\Models\PretestAttempt;
use App\Models\PretestOption;
use App\Models\PretestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PretestQuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat melihat daftar soal Pretest
     * beserta ringkasan datanya.
     */
    public function test_teacher_can_view_pretest_question_list(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $question = $this->createQuestion(
            $mission
        );

        $this->createOptions($question);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.pretest.index',
                    $mission
                )
            );

        $response->assertOk();

        $response->assertViewIs(
            'teacher.missions.pretest.index'
        );

        $response->assertViewHas(
            'totalQuestionCount',
            1
        );

        $response->assertViewHas(
            'activeQuestionCount',
            1
        );

        $response->assertViewHas(
            'completeQuestionCount',
            1
        );

        $response->assertViewHas(
            'totalOptionCount',
            4
        );

        $response->assertSee(
            $question->question_text
        );
    }

    /**
     * Guru dapat membuka formulir dan menambahkan
     * soal Pretest dengan empat pilihan.
     */
    public function test_teacher_can_create_pretest_question(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $existingQuestion = $this->createQuestion(
            $mission
        );

        $this->createOptions($existingQuestion);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.pretest.create',
                    $mission
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.pretest.create'
        );

        $createResponse->assertViewHas(
            'nextSequence',
            2
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.pretest.store',
                    $mission
                ),
                [
                    'question_text' =>
                        'Struktur manakah yang digunakan untuk mengulang perintah?',
                    'sequence' => 2,
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
                'teacher.missions.pretest.index',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'success',
            'Soal Pretest berhasil ditambahkan.'
        );

        $newQuestion = PretestQuestion::query()
            ->where('mission_id', $mission->id)
            ->where('sequence', 2)
            ->first();

        $this->assertNotNull($newQuestion);

        $this->assertDatabaseHas(
            'pretest_questions',
            [
                'id' => $newQuestion->id,
                'question_text' =>
                    'Struktur manakah yang digunakan untuk mengulang perintah?',
                'sequence' => 2,
                'is_active' => true,
            ]
        );

        $this->assertDatabaseCount(
            'pretest_options',
            8
        );

        $this->assertDatabaseHas(
            'pretest_options',
            [
                'pretest_question_id' =>
                    $newQuestion->id,
                'option_text' =>
                    'Struktur perulangan',
                'sequence' => 2,
                'is_correct' => true,
            ]
        );
    }

    /**
 * Soal Pretest aktif baru tidak boleh ditambahkan
 * setelah peserta didik mulai mengerjakan Pretest.
 */
public function test_teacher_cannot_add_active_pretest_question_after_student_attempt_started(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $question = $this->createQuestion(
        $mission
    );

    $this->createOptions(
        $question
    );

    PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PretestAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
        'total_questions' => 1,
        'correct_answers' => 0,
        'question_order' => [
            $question->id,
        ],
    ]);

    $createRoute = route(
        'teacher.missions.pretest.create',
        $mission
    );

    $response = $this
        ->actingAs($teacher)
        ->from($createRoute)
        ->post(
            route(
                'teacher.missions.pretest.store',
                $mission
            ),
            [
                'question_text' =>
                    'Soal aktif baru setelah siswa mulai.',
                'sequence' => 2,
                'is_active' => '1',
                'correct_option_index' => 0,
                'options' => [
                    [
                        'option_text' =>
                            'Pilihan pertama',
                        'sequence' => 1,
                    ],
                    [
                        'option_text' =>
                            'Pilihan kedua',
                        'sequence' => 2,
                    ],
                    [
                        'option_text' =>
                            'Pilihan ketiga',
                        'sequence' => 3,
                    ],
                    [
                        'option_text' =>
                            'Pilihan keempat',
                        'sequence' => 4,
                    ],
                ],
            ]
        );

    $response->assertRedirect(
        $createRoute
    );

    $response->assertSessionHas(
        'error',
        'Soal Pretest aktif tidak dapat ditambahkan karena sudah ada peserta didik yang memulai Pretest.'
    );

    $this->assertDatabaseMissing(
        'pretest_questions',
        [
            'mission_id' => $mission->id,
            'question_text' =>
                'Soal aktif baru setelah siswa mulai.',
        ]
    );
}
    /**
 * Soal Pretest tidak boleh diubah setelah
 * peserta didik mulai mengerjakan Pretest.
 */
public function test_teacher_cannot_update_pretest_question_after_student_attempt_started(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $question = $this->createQuestion(
        $mission
    );

    $options = $this->createOptions(
        $question
    );

    PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PretestAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
        'total_questions' => 1,
        'correct_answers' => 0,
        'question_order' => [
            $question->id,
        ],
    ]);

    $editRoute = route(
        'teacher.missions.pretest.edit',
        [
            'mission' => $mission,
            'question' => $question,
        ]
    );

    $response = $this
        ->actingAs($teacher)
        ->from($editRoute)
        ->put(
            route(
                'teacher.missions.pretest.update',
                [
                    'mission' => $mission,
                    'question' => $question,
                ]
            ),
            [
                'question_text' =>
                    'Soal yang telah diubah guru.',
                'sequence' => 1,
                'is_active' => '1',
                'correct_option_id' =>
                    $options[1]->id,
                'options' => [
                    $options[0]->id => [
                        'option_text' =>
                            'Pilihan pertama diubah',
                        'sequence' => 1,
                    ],
                    $options[1]->id => [
                        'option_text' =>
                            'Pilihan kedua diubah',
                        'sequence' => 2,
                    ],
                    $options[2]->id => [
                        'option_text' =>
                            'Pilihan ketiga diubah',
                        'sequence' => 3,
                    ],
                    $options[3]->id => [
                        'option_text' =>
                            'Pilihan keempat diubah',
                        'sequence' => 4,
                    ],
                ],
            ]
        );

    $response->assertRedirect(
        $editRoute
    );

    $response->assertSessionHas(
        'error',
        'Soal Pretest tidak dapat diubah karena sudah ada peserta didik yang memulai Pretest.'
    );

    $this->assertDatabaseHas(
        'pretest_questions',
        [
            'id' => $question->id,
            'question_text' =>
                'Aktivitas manakah yang menggunakan perulangan?',
            'is_active' => true,
        ]
    );

    $this->assertDatabaseHas(
        'pretest_options',
        [
            'id' => $options[0]->id,
            'option_text' =>
                'Menampilkan angka secara berulang',
            'is_correct' => true,
        ]
    );

    $this->assertDatabaseHas(
        'pretest_options',
        [
            'id' => $options[1]->id,
            'option_text' =>
                'Menyimpan satu nilai',
            'is_correct' => false,
        ]
    );
}

/**
 * Soal Pretest tidak boleh dihapus setelah
 * peserta didik mulai mengerjakan Pretest.
 */
public function test_teacher_cannot_delete_pretest_question_after_student_attempt_started(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $question = $this->createQuestion(
        $mission
    );

    $this->createOptions(
        $question
    );

    PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PretestAttempt::STATUS_IN_PROGRESS,
        'started_at' => now(),
        'total_questions' => 1,
        'correct_answers' => 0,
        'question_order' => [
            $question->id,
        ],
    ]);

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.pretest.destroy',
                [
                    'mission' => $mission,
                    'question' => $question,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.pretest.index',
            $mission
        )
    );

    $response->assertSessionHas(
        'error',
        'Soal Pretest tidak dapat dihapus karena sudah ada peserta didik yang memulai Pretest.'
    );

    $this->assertDatabaseHas(
        'pretest_questions',
        [
            'id' => $question->id,
        ]
    );
}
/**
 * Guru dapat menghapus soal Pretest yang belum
 * pernah memiliki jawaban peserta didik.
 */
public function test_teacher_can_delete_unused_pretest_question(): void
{
    $teacher = $this->createTeacher();

    $mission = $this->createMission();

    $question = $this->createQuestion(
        $mission
    );

    $options = $this->createOptions(
        $question
    );

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.pretest.destroy',
                [
                    'mission' => $mission,
                    'question' => $question,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.pretest.index',
            $mission
        )
    );

    $response->assertSessionHas(
        'success',
        'Soal Pretest berhasil dihapus.'
    );

    $this->assertDatabaseMissing(
        'pretest_questions',
        [
            'id' => $question->id,
        ]
    );

    foreach ($options as $option) {
        $this->assertDatabaseMissing(
            'pretest_options',
            [
                'id' => $option->id,
            ]
        );
    }
}

/**
 * Soal Pretest yang sudah memiliki jawaban
 * peserta didik tidak boleh dihapus.
 */
public function test_teacher_cannot_delete_answered_pretest_question(): void
{
    $teacher = $this->createTeacher();

    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createMission();

    $question = $this->createQuestion(
        $mission
    );

    $options = $this->createOptions(
        $question
    );

    $attempt = PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PretestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(5),
        'completed_at' => now(),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
    ]);

    PretestAnswer::create([
        'pretest_attempt_id' => $attempt->id,
        'pretest_question_id' => $question->id,
        'selected_option_id' => $options[0]->id,
        'is_correct' => true,
    ]);

    $response = $this
        ->actingAs($teacher)
        ->delete(
            route(
                'teacher.missions.pretest.destroy',
                [
                    'mission' => $mission,
                    'question' => $question,
                ]
            )
        );

    $response->assertRedirect(
        route(
            'teacher.missions.pretest.index',
            $mission
        )
    );

    $response->assertSessionHas(
        'error',
        'Soal Pretest tidak dapat dihapus karena sudah memiliki jawaban peserta didik. Nonaktifkan soal melalui menu Edit Soal.'
    );

    $this->assertDatabaseHas(
        'pretest_questions',
        [
            'id' => $question->id,
        ]
    );

    $this->assertDatabaseHas(
        'pretest_answers',
        [
            'pretest_attempt_id' => $attempt->id,
            'pretest_question_id' => $question->id,
            'selected_option_id' => $options[0]->id,
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
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'kelola-pretest-test',
            'description' =>
                'Misi pengujian pengelolaan Pretest.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu soal Pretest aktif.
     */
    private function createQuestion(
        Mission $mission
    ): PretestQuestion {
        return PretestQuestion::create([
            'mission_id' => $mission->id,
            'question_text' =>
                'Aktivitas manakah yang menggunakan perulangan?',
            'sequence' => 1,
            'is_active' => true,
        ]);
    }

    /**
     * Membuat empat pilihan dengan tepat
     * satu jawaban benar.
     *
     * @return array<int, PretestOption>
     */
    private function createOptions(
        PretestQuestion $question
    ): array {
        return [
            PretestOption::create([
                'pretest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menampilkan angka secara berulang',
                'sequence' => 1,
                'is_correct' => true,
            ]),
            PretestOption::create([
                'pretest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menyimpan satu nilai',
                'sequence' => 2,
                'is_correct' => false,
            ]),
            PretestOption::create([
                'pretest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Memilih satu kondisi',
                'sequence' => 3,
                'is_correct' => false,
            ]),
            PretestOption::create([
                'pretest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menghentikan program',
                'sequence' => 4,
                'is_correct' => false,
            ]),
        ];
    }
}
