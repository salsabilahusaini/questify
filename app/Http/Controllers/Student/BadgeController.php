<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\FinalChallengeAttempt;
use App\Models\MissionCompletion;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BadgeController extends Controller
{
    /**
     * Menampilkan koleksi badge dan memberikan badge
     * yang kriterianya telah dipenuhi siswa.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

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

        DB::transaction(function () use (
            $user,
            $badges,
            $completedMissionCount,
            $totalXp,
            $highestChallengeScore
        ): void {
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
        });

        $ownedBadgeIds = UserBadge::query()
            ->where('user_id', $user->id)
            ->pluck('badge_id');

        $userBadges = UserBadge::query()
            ->where('user_id', $user->id)
            ->with('badge')
            ->latest('awarded_at')
            ->get();

        return view('student.badges.index', [
            'badges' => $badges,
            'ownedBadgeIds' => $ownedBadgeIds,
            'userBadges' => $userBadges,
            'completedMissionCount' => $completedMissionCount,
            'totalXp' => $totalXp,
            'highestChallengeScore' =>
                $highestChallengeScore,
        ]);
    }
}
