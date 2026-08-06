<?php

namespace Tests\Feature\Student;

use App\Models\Badge;
use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Badge aktif tetap terkunci ketika siswa
     * belum memenuhi kriterianya.
     */
    public function test_badge_remains_locked_when_student_is_not_eligible(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $badge = Badge::create([
            'name' => 'Mission Starter',
            'slug' => 'mission-starter-test',
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

        $response = $this
            ->actingAs($student)
            ->get(
                route('student.badges.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'completedMissionCount',
            0
        );

        $response->assertViewHas('totalXp', 0);

        $response->assertViewHas(
            'highestChallengeScore',
            0.0
        );

        $response->assertViewHas(
            'ownedBadgeIds',
            fn ($ownedBadgeIds): bool =>
                $ownedBadgeIds->isEmpty()
        );

        $response->assertSeeText($badge->name);
        $response->assertSeeText('Terkunci');

        $this->assertDatabaseMissing(
            'user_badges',
            [
                'user_id' => $student->id,
                'badge_id' => $badge->id,
            ]
        );
    }

    /**
     * Seluruh badge yang kriterianya terpenuhi
     * diberikan otomatis tanpa menjadi ganda.
     */
    public function test_eligible_badges_are_awarded_automatically_without_duplicates(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'badge-perulangan-test',
            'description' =>
                'Misi pengujian pemberian badge.',
            'sequence' => 1,
            'xp_reward' => 150,
            'is_published' => true,
        ]);

        MissionCompletion::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'earned_xp' => 150,
            'completed_at' => now()->subMinutes(10),
        ]);

        $challenge = FinalChallenge::create([
            'mission_id' => $mission->id,
            'title' => 'Perbaiki Program Perulangan',
            'scenario' =>
                'Perbaiki program agar menghasilkan pola bilangan genap.',
            'instructions' =>
                'Perbaiki kode dan jelaskan alasan perbaikannya.',
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
                'Variabel bertambah dua agar menghasilkan bilangan genap dan perulangan berhenti.',
            'started_at' => now()->subMinutes(8),
            'completed_at' => now()->subMinutes(5),
            'score' => 100,
            'earned_points' => 100,
            'feedback' => 'Jawaban sangat baik.',
        ]);

        $missionBadge = Badge::create([
            'name' => 'Mission Starter',
            'slug' => 'mission-starter-award-test',
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

        $xpBadge = Badge::create([
            'name' => 'XP Collector',
            'slug' => 'xp-collector-award-test',
            'description' =>
                'Diperoleh setelah mengumpulkan 100 XP.',
            'icon' => 'star',
            'theme' => 'amber',
            'criteria_type' =>
                Badge::CRITERIA_TOTAL_XP,
            'criteria_value' => 100,
            'sequence' => 2,
            'is_active' => true,
        ]);

        $challengeBadge = Badge::create([
            'name' => 'Perfect Challenger',
            'slug' => 'perfect-challenger-award-test',
            'description' =>
                'Diperoleh setelah meraih nilai sempurna.',
            'icon' => 'trophy',
            'theme' => 'violet',
            'criteria_type' =>
                Badge::CRITERIA_PERFECT_CHALLENGE,
            'criteria_value' => 100,
            'sequence' => 3,
            'is_active' => true,
        ]);

        $inactiveBadge = Badge::create([
            'name' => 'Badge Tidak Aktif',
            'slug' => 'inactive-badge-award-test',
            'description' =>
                'Badge ini tidak boleh diberikan.',
            'icon' => 'star',
            'theme' => 'amber',
            'criteria_type' =>
                Badge::CRITERIA_TOTAL_XP,
            'criteria_value' => 1,
            'sequence' => 4,
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route('student.badges.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'completedMissionCount',
            1
        );

        $response->assertViewHas('totalXp', 150);

        $response->assertViewHas(
            'highestChallengeScore',
            100.0
        );

        $response->assertViewHas(
            'ownedBadgeIds',
            fn ($ownedBadgeIds): bool =>
                $ownedBadgeIds->count() === 3
                && $ownedBadgeIds->contains(
                    $missionBadge->id
                )
                && $ownedBadgeIds->contains(
                    $xpBadge->id
                )
                && $ownedBadgeIds->contains(
                    $challengeBadge->id
                )
        );

        $response->assertSeeText($missionBadge->name);
        $response->assertSeeText($xpBadge->name);
        $response->assertSeeText($challengeBadge->name);

        $response->assertDontSeeText(
            $inactiveBadge->name
        );

        $this->assertDatabaseHas(
            'user_badges',
            [
                'user_id' => $student->id,
                'badge_id' => $missionBadge->id,
            ]
        );

        $this->assertDatabaseHas(
            'user_badges',
            [
                'user_id' => $student->id,
                'badge_id' => $xpBadge->id,
            ]
        );

        $this->assertDatabaseHas(
            'user_badges',
            [
                'user_id' => $student->id,
                'badge_id' => $challengeBadge->id,
            ]
        );

        $this->assertDatabaseMissing(
            'user_badges',
            [
                'user_id' => $student->id,
                'badge_id' => $inactiveBadge->id,
            ]
        );

        /*
         * Membuka halaman kembali tidak boleh
         * memberikan badge yang sama dua kali.
         */
        $this
            ->actingAs($student)
            ->get(
                route('student.badges.index')
            )
            ->assertOk();

        $this->assertSame(
            3,
            UserBadge::query()
                ->where('user_id', $student->id)
                ->count()
        );
    }
}
