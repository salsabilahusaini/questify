<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Mengisi data awal badge.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $badges = [
                [
                    'name' => 'First Mission',
                    'slug' => 'first-mission',
                    'description' =>
                        'Diperoleh setelah menyelesaikan misi pembelajaran pertama.',
                    'icon' => 'flag',
                    'theme' => 'blue',
                    'criteria_type' =>
                        Badge::CRITERIA_MISSIONS_COMPLETED,
                    'criteria_value' => 1,
                    'sequence' => 1,
                    'is_active' => true,
                ],
                [
                    'name' => 'XP Collector',
                    'slug' => 'xp-collector',
                    'description' =>
                        'Diperoleh setelah mengumpulkan minimal 100 XP.',
                    'icon' => 'star',
                    'theme' => 'amber',
                    'criteria_type' =>
                        Badge::CRITERIA_TOTAL_XP,
                    'criteria_value' => 100,
                    'sequence' => 2,
                    'is_active' => true,
                ],
                [
                    'name' => 'Perfect Challenger',
                    'slug' => 'perfect-challenger',
                    'description' =>
                        'Diperoleh setelah meraih nilai sempurna pada Final Challenge.',
                    'icon' => 'trophy',
                    'theme' => 'violet',
                    'criteria_type' =>
                        Badge::CRITERIA_PERFECT_CHALLENGE,
                    'criteria_value' => 100,
                    'sequence' => 3,
                    'is_active' => true,
                ],
            ];

            foreach ($badges as $badgeData) {
                Badge::updateOrCreate(
                    [
                        'slug' => $badgeData['slug'],
                    ],
                    $badgeData
                );
            }

            Badge::query()
                ->whereNotIn(
                    'slug',
                    collect($badges)->pluck('slug')
                )
                ->update([
                    'is_active' => false,
                ]);
        });
    }
}
