<?php

namespace Tests\Feature\Student;

use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\PosttestAttempt;
use App\Models\PretestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MissionDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Hanya Pretest yang tersedia ketika siswa
     * belum menyelesaikan tahapan apa pun.
     */
    public function test_mission_steps_are_locked_in_the_correct_order(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createPublishedMission();

        $response = $this
            ->actingAs($student)
            ->get(
                route(
                    'student.missions.show',
                    [
                        'mission' => $mission->slug,
                    ]
                )
            );

        $response->assertOk();

        $response->assertViewHas(
            'steps',
            function (array $steps): bool {
                $statuses = collect($steps)
                    ->pluck('status', 'key');

                return $statuses->get('pretest')
                        === 'available'
                    && $statuses->get('material')
                        === 'locked'
                    && $statuses->get('micro-exercise')
                        === 'locked'
                    && $statuses->get('quiz')
                        === 'locked'
                    && $statuses->get('final-challenge')
                        === 'locked'
                    && $statuses->get('posttest')
                        === 'locked';
            }
        );

        $response->assertSeeText('Buka Pretest');

        $response->assertDontSeeText(
            'Buka Materi Prakelas'
        );
    }

    /**
 * Deskripsi Materi Prakelas tidak boleh
 * terikat pada materi Perulangan.
 */
public function test_material_step_description_is_generic_for_any_mission(): void
{
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = Mission::create([
        'title' => 'Memahami Konsep Array',
        'slug' => 'detail-misi-array-test',
        'description' =>
            'Misi pengujian materi Array.',
        'sequence' => 2,
        'xp_reward' => 100,
        'is_published' => true,
    ]);

    $response = $this
        ->actingAs($student)
        ->get(
            route(
                'student.missions.show',
                [
                    'mission' => $mission->slug,
                ]
            )
        );

    $response->assertOk();

    $response->assertViewHas(
        'steps',
        function (array $steps): bool {
            $materialStep = collect($steps)
                ->firstWhere(
                    'key',
                    'material'
                );

            return $materialStep !== null
                && $materialStep['description']
                    === 'Pelajari materi melalui bacaan bertahap dan checkpoint.';
        }
    );
}

    /**
     * Seluruh tahap yang telah selesai dapat
     * dibuka kembali dari halaman Detail Misi.
     */
    public function test_completed_mission_steps_can_be_opened_again(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createPublishedMission();

        $materialSectionId = DB::table(
            'material_sections'
        )->insertGetId([
            'mission_id' => $mission->id,
            'title' => 'Materi Perulangan',
            'slug' => 'materi-perulangan-detail-test',
            'description' =>
                'Bagian materi untuk pengujian.',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $materialBlockId = DB::table(
            'material_blocks'
        )->insertGetId([
            'material_section_id' =>
                $materialSectionId,
            'type' => 'paragraph',
            'title' => 'Pengertian Perulangan',
            'content' =>
                'Perulangan menjalankan instruksi berulang.',
            'sequence' => 1,
            'minimum_read_seconds' => 10,
            'is_required' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        PretestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                PretestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(50),
            'completed_at' => now()->subMinutes(45),
            'total_questions' => 10,
            'correct_answers' => 6,
            'score' => 60,
        ]);

        MaterialBlockProgress::create([
            'user_id' => $student->id,
            'material_block_id' => $materialBlockId,
            'first_opened_at' => now()->subMinutes(40),
            'last_activity_at' => now()->subMinutes(35),
            'total_read_seconds' => 30,
            'visit_count' => 1,
            'is_completed' => true,
            'completed_at' => now()->subMinutes(35),
        ]);

        MicroExerciseAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                MicroExerciseAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now()->subMinutes(25),
            'total_questions' => 3,
            'correct_answers' => 3,
            'score' => 100,
            'earned_points' => 30,
        ]);

        QuizAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(24),
            'completed_at' => now()->subMinutes(20),
            'total_questions' => 5,
            'correct_answers' => 4,
            'score' => 80,
            'earned_points' => 40,
        ]);

        $challenge = FinalChallenge::create([
            'mission_id' => $mission->id,
            'title' =>
                'Perbaiki Program Perulangan',
            'scenario' =>
                'Perbaiki program agar menghasilkan pola yang tepat.',
            'instructions' =>
                'Perbaiki kode dan jelaskan alasannya.',
            'starter_code' =>
                'for ($i = 2; $i <= 20; $i--) {}',
            'expected_output' =>
                '2 4 6 8 10 12 14 16 18 20',
            'max_points' => 100,
            'is_active' => true,
        ]);

        FinalChallengeAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'final_challenge_id' => $challenge->id,
            'status' =>
                FinalChallengeAttempt::STATUS_COMPLETED,
            'answer_code' =>
                'for ($i = 2; $i <= 20; $i += 2) {}',
            'answer_output' =>
                '2 4 6 8 10 12 14 16 18 20',
            'answer_explanation' =>
                'Nilai variabel bertambah dua agar menghasilkan bilangan genap.',
            'started_at' => now()->subMinutes(19),
            'completed_at' => now()->subMinutes(15),
            'score' => 100,
            'earned_points' => 100,
            'feedback' => 'Jawaban sangat baik.',
        ]);

        PosttestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                PosttestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 10,
            'correct_answers' => 8,
            'score' => 80,
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route(
                    'student.missions.show',
                    [
                        'mission' => $mission->slug,
                    ]
                )
            );

        $response->assertOk();

        $response->assertViewHas(
            'steps',
            function (array $steps): bool {
                return count($steps) === 6
                    && collect($steps)->every(
                        fn (array $step): bool =>
                            $step['status'] === 'completed'
                            && ! empty($step['url'])
                    );
            }
        );

        $response->assertSeeText(
            'Lihat Kembali Pretest'
        );

        $response->assertSeeText(
            'Lihat Kembali Materi Prakelas'
        );

        $response->assertSeeText(
            'Lihat Kembali Latihan Mikro'
        );

        $response->assertSeeText(
            'Lihat Kembali Kuis Materi'
        );

        $response->assertSeeText(
            'Lihat Kembali Final Challenge'
        );

        $response->assertSeeText(
            'Lihat Kembali Posttest'
        );
    }

    /**
     * Membuat satu misi yang dipublikasikan.
     */
    private function createPublishedMission(): Mission
    {
        return Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'detail-misi-perulangan-test',
            'description' =>
                'Misi pengujian alur pembelajaran.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }
}
