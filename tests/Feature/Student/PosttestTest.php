<?php

namespace Tests\Feature\Student;

use App\Models\Badge;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PosttestAttempt;
use App\Models\PosttestOption;
use App\Models\PosttestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PosttestTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Mission $mission;

    private PosttestAttempt $posttestAttempt;

    /**
     * Menyiapkan siswa, misi, Final Challenge selesai,
     * dan pengerjaan Posttest untuk setiap pengujian.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $this->mission = Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'memahami-konsep-perulangan-test',
            'description' => 'Misi pengujian Posttest.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $finalChallengeId = DB::table(
            'final_challenges'
        )->insertGetId([
            'mission_id' => $this->mission->id,
            'title' => 'Final Challenge Perulangan',
            'scenario' => 'Selesaikan permasalahan perulangan.',
            'max_points' => 100,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FinalChallengeAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'final_challenge_id' => $finalChallengeId,
            'status' =>
                FinalChallengeAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'score' => 100,
            'earned_points' => 100,
        ]);

        $this->posttestAttempt = PosttestAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'status' => PosttestAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'total_questions' => 0,
            'correct_answers' => 0,
        ]);
    }
    /**
 * Halaman Posttest tidak dapat dibuka
 * sebelum Final Challenge diselesaikan.
 */
public function test_posttest_page_is_rejected_before_final_challenge_is_completed(): void
{
    FinalChallengeAttempt::query()
        ->where('user_id', $this->student->id)
        ->where('mission_id', $this->mission->id)
        ->delete();

    $response = $this
        ->actingAs($this->student)
        ->get(
            route(
                'student.missions.posttest.show',
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
        'Selesaikan Final Challenge terlebih dahulu.'
    );
}

    /**
 * Soal Posttest ditampilkan berdasarkan urutan
 * yang tersimpan pada attempt siswa.
 */
public function test_posttest_questions_follow_saved_question_order(): void
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
    $this->posttestAttempt->update([
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
                'student.missions.posttest.questions',
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
     * sebelum menyelesaikan Posttest.
     */
    public function test_student_must_answer_all_posttest_questions(): void
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
            'student.missions.posttest.questions',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $response = $this
            ->actingAs($this->student)
            ->from($questionsRoute)
            ->post(
                route(
                    'student.missions.posttest.submit',
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
            'posttest_attempts',
            [
                'id' => $this->posttestAttempt->id,
                'status' =>
                    PosttestAttempt::STATUS_IN_PROGRESS,
            ]
        );

        $this->assertDatabaseCount(
            'posttest_answers',
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
     * Posttest dengan beberapa soal dinilai dengan benar,
     * menyimpan seluruh jawaban, dan menyelesaikan misi.
     */
    public function test_multiple_posttest_questions_are_scored_and_complete_the_mission(): void
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

        $missionBadge = Badge::create([
    'name' => 'Misi Pertama',
    'slug' => 'posttest-misi-pertama-test',
    'description' =>
        'Diperoleh setelah menyelesaikan satu misi.',
    'icon' => 'flag',
    'theme' => 'blue',
    'criteria_type' =>
        Badge::CRITERIA_MISSIONS_COMPLETED,
    'criteria_value' => 1,
    'sequence' => 1,
    'is_active' => true,
]);

        $answers = [
            $firstQuestion->id =>
                $firstCorrectOption->id,
            $secondQuestion->id =>
                $secondWrongOption->id,
            $thirdQuestion->id =>
                $thirdCorrectOption->id,
        ];

        $submitRoute = route(
            'student.missions.posttest.submit',
            [
                'mission' => $this->mission->slug,
            ]
        );

        $showRoute = route(
            'student.missions.posttest.show',
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
            'Posttest berhasil diselesaikan.'
        );

        $attempt = $this->posttestAttempt->fresh();

        $this->assertSame(
            PosttestAttempt::STATUS_COMPLETED,
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
            'posttest_answers',
            3
        );

        $this->assertDatabaseHas(
            'mission_completions',
            [
                'user_id' => $this->student->id,
                'mission_id' => $this->mission->id,
                'earned_xp' => 100,
            ]
        );
        $this->assertDatabaseHas(
    'user_badges',
    [
        'user_id' => $this->student->id,
        'badge_id' => $missionBadge->id,
    ]
);

        /*
         * Mengirim ulang jawaban tidak boleh membuat
         * MissionCompletion atau jawaban menjadi ganda.
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

        $completionCount = MissionCompletion::query()
            ->where('user_id', $this->student->id)
            ->where('mission_id', $this->mission->id)
            ->count();

        $this->assertSame(
            1,
            $completionCount
        );

        $this->assertDatabaseCount(
            'posttest_answers',
            3
        );

        $awardedBadgeCount = DB::table('user_badges')
    ->where('user_id', $this->student->id)
    ->where('badge_id', $missionBadge->id)
    ->count();

$this->assertSame(
    1,
    $awardedBadgeCount
);
    }

    /**
     * Membuat satu soal Posttest dengan
     * satu opsi benar dan satu opsi salah.
     *
     * @return array{
     *     0: PosttestQuestion,
     *     1: PosttestOption,
     *     2: PosttestOption
     * }
     */
    /**
 * Submit Posttest secara langsung ditolak
 * sebelum Final Challenge diselesaikan.
 */
public function test_direct_submission_is_rejected_before_final_challenge_is_completed(): void
{
    FinalChallengeAttempt::query()
        ->where('user_id', $this->student->id)
        ->where('mission_id', $this->mission->id)
        ->delete();

    [
        $question,
        $correctOption,
    ] = $this->createQuestion(
        sequence: 1,
        questionText: 'Soal Posttest.'
    );

    $response = $this
        ->actingAs($this->student)
        ->post(
            route(
                'student.missions.posttest.submit',
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
        'Selesaikan Final Challenge terlebih dahulu.'
    );

    $this->assertDatabaseHas(
        'posttest_attempts',
        [
            'id' => $this->posttestAttempt->id,
            'status' =>
                PosttestAttempt::STATUS_IN_PROGRESS,
        ]
    );

    $this->assertDatabaseCount(
        'posttest_answers',
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
    private function createQuestion(
        int $sequence,
        string $questionText
    ): array {
        $question = PosttestQuestion::create([
            'mission_id' => $this->mission->id,
            'question_text' => $questionText,
            'sequence' => $sequence,
            'is_active' => true,
        ]);

        $correctOption = PosttestOption::create([
            'posttest_question_id' => $question->id,
            'option_text' => 'Jawaban benar',
            'sequence' => 1,
            'is_correct' => true,
        ]);

        $wrongOption = PosttestOption::create([
            'posttest_question_id' => $question->id,
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
