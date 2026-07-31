<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\FinalChallengeAttempt;
use App\Models\MissionCompletion;
use App\Models\User;
use App\Models\UserBadge;

class BadgeAwardService
{
    /**
     * Memberikan seluruh badge yang kriterianya
     * telah dipenuhi oleh peserta didik.
     */
    public function awardEligibleBadges(User $user): void
    {
        $completedMissionCount = MissionCompletion::query()
            ->where('user_id', $user->id)
            ->count();

        $totalXp = (int) MissionCompletion::query()
            ->where('user_id', $user->id)
            ->sum('earned_xp');

        $highestChallengeScore = (float) (
            FinalChallengeAttempt::query()
                ->where('user_id', $user->id)
                ->where(
                    'status',
                    FinalChallengeAttempt::STATUS_COMPLETED
                )
                ->max('score') ?? 0
        );

        $badges = Badge::query()
            ->where('is_active', true)
            ->orderBy('sequence')
            ->get();

        foreach ($badges as $badge) {
            $isEligible = match ($badge->criteria_type) {
                Badge::CRITERIA_MISSIONS_COMPLETED =>
                    $completedMissionCount
                        >= $badge->criteria_value,

                Badge::CRITERIA_TOTAL_XP =>
                    $totalXp
                        >= $badge->criteria_value,

                Badge::CRITERIA_PERFECT_CHALLENGE =>
                    $highestChallengeScore
                        >= $badge->criteria_value,

                default => false,
            };

            if ($isEligible) {
                UserBadge::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'badge_id' => $badge->id,
                    ],
                    [
                        'awarded_at' => now(),
                    ]
                );
            }
        }
    }
}
