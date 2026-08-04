<?php

namespace Tests\Feature\Services;

use App\Models\Badge;
use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\BadgeAwardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeAwardServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seluruh badge aktif diberikan ketika
     * masing-masing kriterianya telah terpenuhi.
     */
    public function test_eligible_active_badges_are_awarded(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createMission();

        MissionCompletion::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'earned_xp' => 150,
            'completed_at' => now(),
        ]);

        $finalChallenge = $this->createFinalChallenge(
            $mission
        );

        FinalChallengeAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'final_challenge_id' =>
                $finalChallenge->id,
            'status' =>
                FinalChallengeAttempt::STATUS_COMPLETED,
            'answer_code' =>
                'for ($i = 2; $i <= 20; $i += 2) {}',
            'answer_output' =>
                '2 4 6 8 10 12 14 16 18 20',
            'answer_explanation' =>
                'Variabel bertambah dua setiap perulangan.',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'score' => 100,
            'earned_points' => 100,
            'feedback' => 'Jawaban sempurna.',
        ]);

        $missionBadge = $this->createBadge(
            name: 'Penakluk Misi',
            slug: 'penakluk-misi-test',
            criteriaType:
                Badge::CRITERIA_MISSIONS_COMPLETED,
            criteriaValue: 1,
            sequence: 1
        );

        $xpBadge = $this->createBadge(
            name: 'Pengumpul XP',
            slug: 'pengumpul-xp-test',
            criteriaType:
                Badge::CRITERIA_TOTAL_XP,
            criteriaValue: 100,
            sequence: 2
        );

        $challengeBadge = $this->createBadge(
            name: 'Perfect Challenge',
            slug: 'perfect-challenge-test',
            criteriaType:
                Badge::CRITERIA_PERFECT_CHALLENGE,
            criteriaValue: 100,
            sequence: 3
        );

        app(BadgeAwardService::class)
            ->awardEligibleBadges($student);

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

        $this->assertSame(
            3,
            UserBadge::query()
                ->where('user_id', $student->id)
                ->count()
        );
    }

    /**
     * Badge tidak diberikan ketika kriteria belum
     * terpenuhi atau badge berstatus tidak aktif.
     */
    public function test_ineligible_and_inactive_badges_are_not_awarded(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createMission();

        $finalChallenge = $this->createFinalChallenge(
            $mission
        );

        /*
         * Nilai 100 tidak boleh dihitung karena
         * attempt masih berstatus in_progress.
         */
        FinalChallengeAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'final_challenge_id' =>
                $finalChallenge->id,
            'status' =>
                FinalChallengeAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'score' => 100,
            'earned_points' => 100,
        ]);

        $this->createBadge(
            name: 'XP Tinggi',
            slug: 'xp-tinggi-test',
            criteriaType: Badge::CRITERIA_TOTAL_XP,
            criteriaValue: 500,
            sequence: 1
        );

        $this->createBadge(
            name: 'Challenge Sempurna',
            slug: 'challenge-sempurna-test',
            criteriaType:
                Badge::CRITERIA_PERFECT_CHALLENGE,
            criteriaValue: 100,
            sequence: 2
        );

        $this->createBadge(
            name: 'Badge Tidak Aktif',
            slug: 'badge-tidak-aktif-test',
            criteriaType:
                Badge::CRITERIA_MISSIONS_COMPLETED,
            criteriaValue: 0,
            sequence: 3,
            isActive: false
        );

        app(BadgeAwardService::class)
            ->awardEligibleBadges($student);

        $this->assertDatabaseMissing(
            'user_badges',
            [
                'user_id' => $student->id,
            ]
        );

        $this->assertDatabaseCount(
            'user_badges',
            0
        );
    }

    /**
     * Pemanggilan service berulang tidak boleh
     * menggandakan badge yang sama.
     */
    public function test_repeated_award_checks_do_not_duplicate_badges(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createMission();

        MissionCompletion::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'earned_xp' => 100,
            'completed_at' => now(),
        ]);

        $badge = $this->createBadge(
            name: 'Misi Pertama',
            slug: 'misi-pertama-test',
            criteriaType:
                Badge::CRITERIA_MISSIONS_COMPLETED,
            criteriaValue: 1,
            sequence: 1
        );

        $service = app(BadgeAwardService::class);

        $service->awardEligibleBadges($student);
        $service->awardEligibleBadges($student);

        $badgeCount = UserBadge::query()
            ->where('user_id', $student->id)
            ->where('badge_id', $badge->id)
            ->count();

        $this->assertSame(
            1,
            $badgeCount
        );

        $this->assertDatabaseCount(
            'user_badges',
            1
        );
    }

    /**
     * Membuat satu misi yang dipublikasikan.
     */
    private function createMission(): Mission
    {
        return Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'badge-service-mission-test',
            'description' =>
                'Misi pengujian pemberian badge.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat Final Challenge untuk misi.
     */
    private function createFinalChallenge(
        Mission $mission
    ): FinalChallenge {
        return FinalChallenge::create([
            'mission_id' => $mission->id,
            'title' =>
                'Perbaiki Program Nomor Antrean Genap',
            'scenario' =>
                'Perbaiki program agar menampilkan bilangan genap.',
            'instructions' =>
                'Perbaiki kode dan jelaskan hasilnya.',
            'starter_code' => <<<'CODE'
for ($i = 2; $i <= 20; $i--) {
    echo $i . " ";
}
CODE,
            'expected_output' =>
                '2 4 6 8 10 12 14 16 18 20',
            'max_points' => 100,
            'is_active' => true,
        ]);
    }

    /**
     * Membuat satu data badge.
     */
    private function createBadge(
        string $name,
        string $slug,
        string $criteriaType,
        int $criteriaValue,
        int $sequence,
        bool $isActive = true
    ): Badge {
        return Badge::create([
            'name' => $name,
            'slug' => $slug,
            'description' =>
                'Badge untuk kebutuhan pengujian.',
            'icon' => 'star',
            'theme' => 'violet',
            'criteria_type' => $criteriaType,
            'criteria_value' => $criteriaValue,
            'sequence' => $sequence,
            'is_active' => $isActive,
        ]);
    }
}
