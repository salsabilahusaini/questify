<?php

namespace Tests\Feature\Teacher;

use App\Models\Mission;
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
