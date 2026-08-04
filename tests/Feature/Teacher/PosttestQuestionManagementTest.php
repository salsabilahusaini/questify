<?php

namespace Tests\Feature\Teacher;

use App\Models\Mission;
use App\Models\PosttestOption;
use App\Models\PosttestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosttestQuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat melihat daftar soal Posttest
     * beserta ringkasan datanya.
     */
    public function test_teacher_can_view_posttest_question_list(): void
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
                    'teacher.missions.posttest.index',
                    $mission
                )
            );

        $response->assertOk();

        $response->assertViewIs(
            'teacher.missions.posttest.index'
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
     * Guru dapat memperbarui pertanyaan,
     * pilihan, kunci jawaban, dan status soal.
     */
    public function test_teacher_can_update_posttest_question(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $question = $this->createQuestion(
            $mission
        );

        $options = $this->createOptions(
            $question
        );

        $newCorrectOption = $options[1];

        $response = $this
            ->actingAs($teacher)
            ->put(
                route(
                    'teacher.missions.posttest.update',
                    [
                        'mission' => $mission,
                        'question' => $question,
                    ]
                ),
                [
                    'question_text' =>
                        'Struktur manakah yang tepat untuk melakukan pengulangan?',
                    'sequence' => 2,
                    'is_active' => '1',
                    'correct_option_id' =>
                        $newCorrectOption->id,
                    'options' => [
                        $options[0]->id => [
                            'option_text' =>
                                'Struktur percabangan',
                            'sequence' => 1,
                        ],
                        $options[1]->id => [
                            'option_text' =>
                                'Struktur perulangan',
                            'sequence' => 2,
                        ],
                        $options[2]->id => [
                            'option_text' =>
                                'Deklarasi variabel',
                            'sequence' => 3,
                        ],
                        $options[3]->id => [
                            'option_text' =>
                                'Pemanggilan fungsi satu kali',
                            'sequence' => 4,
                        ],
                    ],
                ]
            );

        $response->assertRedirect(
            route(
                'teacher.missions.posttest.index',
                $mission
            )
        );

        $response->assertSessionHas(
            'success',
            'Soal Posttest berhasil diperbarui.'
        );

        $this->assertDatabaseHas(
            'posttest_questions',
            [
                'id' => $question->id,
                'question_text' =>
                    'Struktur manakah yang tepat untuk melakukan pengulangan?',
                'sequence' => 2,
                'is_active' => true,
            ]
        );

        $this->assertDatabaseHas(
            'posttest_options',
            [
                'id' => $newCorrectOption->id,
                'option_text' =>
                    'Struktur perulangan',
                'sequence' => 2,
                'is_correct' => true,
            ]
        );

        $this->assertDatabaseHas(
            'posttest_options',
            [
                'id' => $options[0]->id,
                'is_correct' => false,
            ]
        );
    }

    /**
     * Guru tidak boleh mengedit soal Posttest
     * yang berasal dari misi lain.
     */
    public function test_teacher_cannot_edit_question_from_another_mission(): void
    {
        $teacher = $this->createTeacher();

        $firstMission = $this->createMission();

        $secondMission = Mission::create([
            'title' => 'Misi Array',
            'slug' => 'posttest-array-test',
            'description' =>
                'Misi pengujian Posttest Array.',
            'sequence' => 2,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $question = $this->createQuestion(
            $secondMission
        );

        $response = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.posttest.edit',
                    [
                        'mission' => $firstMission,
                        'question' => $question,
                    ]
                )
            );

        $response->assertNotFound();
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
            'slug' => 'kelola-posttest-test',
            'description' =>
                'Misi pengujian pengelolaan Posttest.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu soal Posttest aktif.
     */
    private function createQuestion(
        Mission $mission
    ): PosttestQuestion {
        return PosttestQuestion::create([
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
     * @return array<int, PosttestOption>
     */
    private function createOptions(
        PosttestQuestion $question
    ): array {
        return [
            PosttestOption::create([
                'posttest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menampilkan angka secara berulang',
                'sequence' => 1,
                'is_correct' => true,
            ]),
            PosttestOption::create([
                'posttest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menyimpan satu nilai',
                'sequence' => 2,
                'is_correct' => false,
            ]),
            PosttestOption::create([
                'posttest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Memilih satu kondisi',
                'sequence' => 3,
                'is_correct' => false,
            ]),
            PosttestOption::create([
                'posttest_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menghentikan program',
                'sequence' => 4,
                'is_correct' => false,
            ]),
        ];
    }
}
