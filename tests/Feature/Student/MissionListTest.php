<?php

namespace Tests\Feature\Student;

use App\Models\Mission;
use App\Models\PretestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Daftar misi hanya menampilkan misi yang
     * dipublikasikan dan mengurutkannya berdasarkan sequence.
     */
    public function test_only_published_missions_are_displayed_in_sequence(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $secondMission = $this->createMission(
            title: 'Misi Kedua',
            slug: 'misi-kedua-list-test',
            sequence: 2
        );

        $firstMission = $this->createMission(
            title: 'Misi Pertama',
            slug: 'misi-pertama-list-test',
            sequence: 1
        );

        $draftMission = $this->createMission(
            title: 'Misi Belum Dipublikasikan',
            slug: 'misi-draft-list-test',
            sequence: 3,
            isPublished: false
        );

        $response = $this
            ->actingAs($student)
            ->get(route('student.missions.index'));

        $response->assertOk();

        $response->assertViewHas(
            'missions',
            function ($missions) use (
                $firstMission,
                $secondMission,
                $draftMission
            ): bool {
                return $missions->pluck('id')->all() === [
                    $firstMission->id,
                    $secondMission->id,
                ]
                    && ! $missions->contains(
                        'id',
                        $draftMission->id
                    );
            }
        );

        $response->assertSeeInOrder([
            'Misi Pertama',
            'Misi Kedua',
        ]);

        $response->assertDontSeeText(
            'Misi Belum Dipublikasikan'
        );

        /*
         * Alur lengkap harus mencantumkan Posttest
         * sebagai tahap keenam.
         */
        $response->assertSeeText('Posttest');
    }

    /**
     * Daftar misi menampilkan progres berdasarkan
     * jumlah tahap pembelajaran yang telah selesai.
     */
    public function test_mission_list_displays_real_student_progress(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createMission(
            title: 'Misi Progres Perulangan',
            slug: 'misi-progres-perulangan-list-test',
            sequence: 1
        );

        /*
         * Satu dari enam tahap telah selesai.
         */
        PretestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' => PretestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 10,
            'correct_answers' => 7,
            'score' => 70,
        ]);

        $response = $this
            ->actingAs($student)
            ->get(route('student.missions.index'));

        $response->assertOk();

        $response->assertViewHas(
            'missions',
            function ($missions) use ($mission): bool {
                $missionItem = $missions->firstWhere(
                    'id',
                    $mission->id
                );

                return $missionItem !== null
                    && $missionItem->completed_steps === 1
                    && $missionItem->total_steps === 6
                    && $missionItem->progress_percentage === 17
                    && $missionItem->progress_label
                        === 'Sedang berlangsung';
            }
        );

        $response->assertSeeText('17%');

        $response->assertSeeText(
            '1 dari 6 tahap selesai'
        );
    }

    /**
     * Membuat data misi untuk pengujian.
     */
    private function createMission(
        string $title,
        string $slug,
        int $sequence,
        bool $isPublished = true
    ): Mission {
        return Mission::create([
            'title' => $title,
            'slug' => $slug,
            'description' =>
                'Misi untuk pengujian daftar misi siswa.',
            'sequence' => $sequence,
            'xp_reward' => 100,
            'is_published' => $isPublished,
        ]);
    }
}
