<?php

namespace Database\Seeders;

use App\Models\Mission;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        Mission::updateOrCreate(
            [
                'slug' => 'memahami-konsep-perulangan',
            ],
            [
                'title' => 'Memahami Konsep Perulangan',
                'description' => 'Pelajari pengertian, fungsi, komponen, kondisi berhenti, variabel pengontrol, dan kesalahan umum pada struktur perulangan.',
                'sequence' => 1,
                'xp_reward' => 100,
                'is_published' => true,
            ],
        );
    }
}
