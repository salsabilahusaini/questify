<?php

namespace Tests\Feature\Student;

use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Siswa yang belum menyelesaikan misi
     * memiliki progres dan XP awal.
     */
    public function test_student_without_completed_mission_has_initial_progress(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'progress-awal-perulangan-test',
            'description' =>
                'Misi pengujian progres awal siswa.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route('student.progress.index')
            );

        $response->assertOk();

        $response->assertViewHas('totalXp', 0);
        $response->assertViewHas('completedMissions', 0);
        $response->assertViewHas(
            'totalPublishedMissions',
            1
        );

        $response->assertViewHas(
            'missionProgressPercentage',
            0.0
        );

        $response->assertViewHas('level', 'Starter');
        $response->assertViewHas('currentLevelXp', 0);
        $response->assertViewHas('nextLevelXp', 100);
        $response->assertViewHas('xpToNextLevel', 100);

        $response->assertViewHas(
            'levelProgressPercentage',
            0.0
        );

        $response->assertSeeText(
            'Belum ada misi yang diselesaikan'
        );
    }

    /**
     * Persentase misi hanya menghitung misi
     * yang masih dipublikasikan, sedangkan XP
     * yang pernah diperoleh tetap dipertahankan.
     */
    public function test_progress_counts_only_published_missions_without_removing_earned_xp(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $publishedMission = Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'progress-misi-terbit-test',
            'description' =>
                'Misi yang masih dipublikasikan.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $unpublishedMission = Mission::create([
            'title' => 'Pengenalan Array',
            'slug' => 'progress-misi-tidak-terbit-test',
            'description' =>
                'Misi yang sudah tidak dipublikasikan.',
            'sequence' => 2,
            'xp_reward' => 50,
            'is_published' => false,
        ]);

        MissionCompletion::create([
            'user_id' => $student->id,
            'mission_id' => $publishedMission->id,
            'earned_xp' => 100,
            'completed_at' => now()->subDay(),
        ]);

        MissionCompletion::create([
            'user_id' => $student->id,
            'mission_id' => $unpublishedMission->id,
            'earned_xp' => 50,
            'completed_at' => now(),
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route('student.progress.index')
            );

        $response->assertOk();

        /*
         * Seluruh XP yang telah diperoleh
         * tetap menjadi milik siswa.
         */
        $response->assertViewHas('totalXp', 150);

        /*
         * Progres misi hanya menghitung satu
         * misi yang masih dipublikasikan.
         */
        $response->assertViewHas(
            'completedMissions',
            1
        );

        $response->assertViewHas(
            'totalPublishedMissions',
            1
        );

        $response->assertViewHas(
            'missionProgressPercentage',
            100.0
        );

        /*
         * 150 XP berada pada level Beginner.
         * Progres level:
         * (150 - 100) / (300 - 100) = 25%.
         */
        $response->assertViewHas(
            'level',
            'Beginner'
        );

        $response->assertViewHas(
            'currentLevelXp',
            100
        );

        $response->assertViewHas(
            'nextLevelXp',
            300
        );

        $response->assertViewHas(
            'xpToNextLevel',
            150
        );

        $response->assertViewHas(
            'levelProgressPercentage',
            25.0
        );

        /*
         * Riwayat tetap menyimpan kedua misi
         * yang pernah diselesaikan.
         */
        $response->assertViewHas(
            'completions',
            fn ($completions): bool =>
                $completions->count() === 2
        );
    }
}
