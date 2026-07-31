<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\FinalChallengeAttempt;
use App\Models\MissionCompletion;
use App\Models\UserBadge;
use App\Services\BadgeAwardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BadgeController extends Controller
{
    /**
     * Menampilkan koleksi badge dan memberikan badge
     * yang kriterianya telah dipenuhi siswa.
     */
    public function index(
    Request $request,
    BadgeAwardService $badgeAwardService
): View
    {
        $user = $request->user();
        $badgeAwardService->awardEligibleBadges($user);

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
