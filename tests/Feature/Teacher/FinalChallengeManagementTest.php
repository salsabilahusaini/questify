<?php

namespace Tests\Feature\Teacher;

use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalChallengeManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat membuka formulir dan menambahkan
     * satu Final Challenge pada misi.
     */
    public function test_teacher_can_create_final_challenge(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.final-challenge.create',
                    $mission
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.final-challenge.create'
        );

        $createResponse->assertViewHas(
            'mission',
            $mission
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.final-challenge.store',
                    $mission
                ),
                [
                    'title' =>
                        'Perbaiki Program Nomor Antrean Genap',
                    'scenario' =>
                        'Sebuah program harus menampilkan nomor antrean genap dari 2 sampai 20.',
                    'instructions' =>
                        'Perbaiki kode, tentukan output, dan jelaskan alasan perbaikannya.',
                    'starter_code' => <<<'CODE'
for ($i = 2; $i <= 20; $i--) {
    echo $i . " ";
}
CODE,
                    'expected_output' =>
                        '2 4 6 8 10 12 14 16 18 20',
                    'max_points' => 100,
                    'is_active' => '1',
                ]
            );

        $storeResponse->assertRedirect(
            route(
                'teacher.missions.final-challenge.index',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'success',
            'Final Challenge berhasil ditambahkan.'
        );

        $this->assertDatabaseHas(
            'final_challenges',
            [
                'mission_id' => $mission->id,
                'title' =>
                    'Perbaiki Program Nomor Antrean Genap',
                'scenario' =>
                    'Sebuah program harus menampilkan nomor antrean genap dari 2 sampai 20.',
                'instructions' =>
                    'Perbaiki kode, tentukan output, dan jelaskan alasan perbaikannya.',
                'expected_output' =>
                    '2 4 6 8 10 12 14 16 18 20',
                'max_points' => 100,
                'is_active' => true,
            ]
        );

        $this->assertSame(
            1,
            FinalChallenge::query()
                ->where('mission_id', $mission->id)
                ->count()
        );
    }

    /**
     * Guru tidak dapat membuat Final Challenge kedua
     * pada misi yang sama.
     */
    public function test_teacher_cannot_create_duplicate_final_challenge(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $existingChallenge = FinalChallenge::create([
            'mission_id' => $mission->id,
            'title' => 'Final Challenge Pertama',
            'scenario' =>
                'Skenario Final Challenge pertama.',
            'instructions' =>
                'Petunjuk Final Challenge pertama.',
            'starter_code' => null,
            'expected_output' => null,
            'max_points' => 100,
            'is_active' => true,
        ]);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.final-challenge.create',
                    $mission
                )
            );

        $createResponse->assertRedirect(
            route(
                'teacher.missions.final-challenge.index',
                $mission
            )
        );

        $createResponse->assertSessionHas(
            'error',
            'Misi ini sudah memiliki Final Challenge. Gunakan menu Edit Final Challenge untuk mengubahnya.'
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.final-challenge.store',
                    $mission
                ),
                [
                    'title' =>
                        'Final Challenge Kedua',
                    'scenario' =>
                        'Skenario Final Challenge kedua.',
                    'instructions' =>
                        'Petunjuk Final Challenge kedua.',
                    'starter_code' => null,
                    'expected_output' => null,
                    'max_points' => 50,
                    'is_active' => '1',
                ]
            );

        $storeResponse->assertRedirect(
            route(
                'teacher.missions.final-challenge.index',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'error',
            'Misi ini sudah memiliki Final Challenge. Gunakan menu Edit Final Challenge untuk mengubahnya.'
        );

        $this->assertSame(
            1,
            FinalChallenge::query()
                ->where('mission_id', $mission->id)
                ->count()
        );

        $this->assertDatabaseHas(
            'final_challenges',
            [
                'id' => $existingChallenge->id,
                'mission_id' => $mission->id,
                'title' => 'Final Challenge Pertama',
                'max_points' => 100,
            ]
        );

        $this->assertDatabaseMissing(
            'final_challenges',
            [
                'mission_id' => $mission->id,
                'title' => 'Final Challenge Kedua',
            ]
        );
    }

    /**
     * Final Challenge tidak boleh diubah setelah
     * peserta didik mulai mengerjakannya.
     */
    public function test_teacher_cannot_update_final_challenge_after_student_attempt_started(): void
    {
        $teacher = $this->createTeacher();

        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createMission();

        $challenge = FinalChallenge::create([
            'mission_id' => $mission->id,
            'title' => 'Final Challenge Perulangan',
            'scenario' =>
                'Selesaikan permasalahan perulangan.',
            'instructions' =>
                'Perbaiki program sesuai skenario.',
            'starter_code' => null,
            'expected_output' => '2 4 6 8 10',
            'max_points' => 100,
            'is_active' => true,
        ]);

        FinalChallengeAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'final_challenge_id' => $challenge->id,
            'status' =>
                FinalChallengeAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'score' => 100,
            'earned_points' => 100,
        ]);

        $editRoute = route(
            'teacher.missions.final-challenge.edit',
            [
                'mission' => $mission,
                'finalChallenge' => $challenge,
            ]
        );

        $response = $this
            ->actingAs($teacher)
            ->from($editRoute)
            ->put(
                route(
                    'teacher.missions.final-challenge.update',
                    [
                        'mission' => $mission,
                        'finalChallenge' => $challenge,
                    ]
                ),
                [
                    'title' =>
                        'Final Challenge yang Diubah',
                    'scenario' =>
                        'Skenario yang telah diubah.',
                    'instructions' =>
                        'Instruksi yang telah diubah.',
                    'starter_code' => null,
                    'expected_output' =>
                        'Output yang telah diubah.',
                    'max_points' => 50,
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect(
            $editRoute
        );

        $response->assertSessionHas(
            'error',
            'Final Challenge tidak dapat diubah karena sudah ada peserta didik yang memulai Final Challenge.'
        );

        $this->assertDatabaseHas(
            'final_challenges',
            [
                'id' => $challenge->id,
                'title' => 'Final Challenge Perulangan',
                'scenario' =>
                    'Selesaikan permasalahan perulangan.',
                'max_points' => 100,
                'is_active' => true,
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
                'kelola-final-challenge-test',
            'description' =>
                'Misi pengujian pengelolaan Final Challenge.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }
}
