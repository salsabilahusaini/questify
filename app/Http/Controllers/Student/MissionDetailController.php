<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\View\View;

class MissionDetailController extends Controller
{
    public function __invoke(Mission $mission): View
    {
        abort_unless($mission->is_published, 404);

        $steps = [
            [
                'number' => 1,
                'key' => 'pretest',
                'title' => 'Pretest',
                'description' => 'Kerjakan tes awal untuk mengukur kemampuan sebelum mempelajari materi.',
                'status' => 'available',
            ],
            [
                'number' => 2,
                'key' => 'material',
                'title' => 'Materi Prakelas',
                'description' => 'Pelajari materi perulangan melalui bacaan bertahap dan checkpoint.',
                'status' => 'locked',
            ],
            [
                'number' => 3,
                'key' => 'micro-exercise',
                'title' => 'Latihan Mikro',
                'description' => 'Selesaikan latihan singkat yang tersedia di antara bagian materi.',
                'status' => 'locked',
            ],
            [
                'number' => 4,
                'key' => 'quiz',
                'title' => 'Kuis Materi',
                'description' => 'Uji pemahaman terhadap konsep yang telah dipelajari.',
                'status' => 'locked',
            ],
            [
                'number' => 5,
                'key' => 'final-challenge',
                'title' => 'Final Challenge',
                'description' => 'Selesaikan tantangan akhir untuk menuntaskan misi dan memperoleh XP.',
                'status' => 'locked',
            ],
        ];

        return view('student.missions.show', [
            'mission' => $mission,
            'steps' => $steps,
        ]);
    }
}
