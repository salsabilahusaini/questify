<?php

namespace Tests\Feature\Teacher;

use App\Models\Mission;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizQuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat melihat daftar soal Kuis Materi
     * beserta ringkasan datanya.
     */
    public function test_teacher_can_view_quiz_question_list(): void
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
                    'teacher.missions.quizzes.index',
                    $mission
                )
            );

        $response->assertOk();

        $response->assertViewIs(
            'teacher.missions.quizzes.index'
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
            'totalPointCount',
            20
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
     * soal Kuis Materi dengan empat pilihan.
     */
    public function test_teacher_can_create_quiz_question(): void
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
                    'teacher.missions.quizzes.create',
                    $mission
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.quizzes.create'
        );

        $createResponse->assertViewHas(
            'nextSequence',
            2
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.quizzes.store',
                    $mission
                ),
                [
                    'question_text' =>
                        'Struktur manakah yang digunakan untuk mengulang perintah?',
                    'sequence' => 2,
                    'points' => 20,
                    'explanation' =>
                        'Struktur perulangan digunakan untuk menjalankan perintah secara berulang.',
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
                'teacher.missions.quizzes.index',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'success',
            'Soal Kuis Materi berhasil ditambahkan.'
        );

        $newQuestion = QuizQuestion::query()
            ->where('mission_id', $mission->id)
            ->where('sequence', 2)
            ->first();

        $this->assertNotNull($newQuestion);

        $this->assertDatabaseHas(
            'quiz_questions',
            [
                'id' => $newQuestion->id,
                'type' =>
                    QuizQuestion::TYPE_MULTIPLE_CHOICE,
                'question_text' =>
                    'Struktur manakah yang digunakan untuk mengulang perintah?',
                'sequence' => 2,
                'points' => 20,
                'explanation' =>
                    'Struktur perulangan digunakan untuk menjalankan perintah secara berulang.',
                'is_active' => true,
            ]
        );

        $this->assertDatabaseCount(
            'quiz_options',
            8
        );

        $this->assertDatabaseHas(
            'quiz_options',
            [
                'quiz_question_id' =>
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
            'slug' => 'kelola-kuis-test',
            'description' =>
                'Misi pengujian pengelolaan Kuis Materi.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu soal Kuis Materi aktif.
     */
    private function createQuestion(
        Mission $mission
    ): QuizQuestion {
        return QuizQuestion::create([
            'mission_id' => $mission->id,
            'type' =>
                QuizQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text' =>
                'Aktivitas manakah yang menggunakan perulangan?',
            'sequence' => 1,
            'points' => 20,
            'explanation' =>
                'Perulangan menjalankan pola yang sama berulang kali.',
            'is_active' => true,
        ]);
    }

    /**
     * Membuat empat pilihan dengan tepat
     * satu jawaban benar.
     *
     * @return array<int, QuizOption>
     */
    private function createOptions(
        QuizQuestion $question
    ): array {
        return [
            QuizOption::create([
                'quiz_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menampilkan angka secara berulang',
                'sequence' => 1,
                'is_correct' => true,
            ]),
            QuizOption::create([
                'quiz_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menyimpan satu nilai',
                'sequence' => 2,
                'is_correct' => false,
            ]),
            QuizOption::create([
                'quiz_question_id' =>
                    $question->id,
                'option_text' =>
                    'Memilih satu kondisi',
                'sequence' => 3,
                'is_correct' => false,
            ]),
            QuizOption::create([
                'quiz_question_id' =>
                    $question->id,
                'option_text' =>
                    'Menghentikan program',
                'sequence' => 4,
                'is_correct' => false,
            ]),
        ];
    }
}
