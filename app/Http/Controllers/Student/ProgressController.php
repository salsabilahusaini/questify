<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\MissionCompletion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressController extends Controller
{
    /**
     * Menampilkan progres penyelesaian misi dan XP siswa.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $completions = MissionCompletion::query()
            ->where('user_id', $user->id)
            ->with([
                'mission:id,title,slug,xp_reward',
            ])
            ->latest('completed_at')
            ->get();

        $totalXp = (int) $completions->sum('earned_xp');

        $completedMissions = $completions->count();

        $totalPublishedMissions = Mission::query()
            ->where('is_published', true)
            ->count();

        $missionProgressPercentage =
            $totalPublishedMissions > 0
                ? round(
                    (
                        $completedMissions
                        / $totalPublishedMissions
                    ) * 100,
                    2
                )
                : 0;

        /*
         * Menentukan level siswa berdasarkan total XP.
         *
         * Starter      : 0–99 XP
         * Beginner     : 100–299 XP
         * Intermediate : 300–599 XP
         * Advanced     : 600 XP atau lebih
         */
        if ($totalXp < 100) {
            $level = 'Starter';
            $currentLevelXp = 0;
            $nextLevelXp = 100;
        } elseif ($totalXp < 300) {
            $level = 'Beginner';
            $currentLevelXp = 100;
            $nextLevelXp = 300;
        } elseif ($totalXp < 600) {
            $level = 'Intermediate';
            $currentLevelXp = 300;
            $nextLevelXp = 600;
        } else {
            $level = 'Advanced';
            $currentLevelXp = 600;
            $nextLevelXp = null;
        }

        if ($nextLevelXp === null) {
            $xpToNextLevel = 0;
            $levelProgressPercentage = 100;
        } else {
            $xpToNextLevel = max(
                0,
                $nextLevelXp - $totalXp
            );

            $levelProgressPercentage = round(
                (
                    ($totalXp - $currentLevelXp)
                    / ($nextLevelXp - $currentLevelXp)
                ) * 100,
                2
            );
        }

        return view('student.progress.index', [
            'completions' => $completions,
            'totalXp' => $totalXp,
            'completedMissions' => $completedMissions,
            'totalPublishedMissions' =>
                $totalPublishedMissions,
            'missionProgressPercentage' =>
                $missionProgressPercentage,
            'level' => $level,
            'currentLevelXp' => $currentLevelXp,
            'nextLevelXp' => $nextLevelXp,
            'xpToNextLevel' => $xpToNextLevel,
            'levelProgressPercentage' =>
                $levelProgressPercentage,
        ]);
    }
}
