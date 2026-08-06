<?php

namespace Tests\Feature\Student;

use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PosttestAttempt;
use App\Models\PretestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EPortfolioTest extends TestCase
{
    use RefreshDatabase;
    /**
 * E-Portfolio menampilkan hasil Pretest, Posttest,
 * dan peningkatan nilai pada misi yang selesai.
 */
public function test_eportfolio_displays_pretest_posttest_and_score_improvement(): void
{
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = Mission::create([
        'title' => 'Memahami Konsep Perulangan',
        'slug' => 'eportfolio-posttest-test',
        'description' =>
            'Misi pengujian hasil belajar E-Portfolio.',
        'sequence' => 1,
        'xp_reward' => 100,
        'is_published' => true,
    ]);

    PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PretestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now()->subMinutes(25),
        'total_questions' => 5,
        'correct_answers' => 3,
        'score' => 60,
    ]);

    PosttestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PosttestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(10),
        'completed_at' => now()->subMinutes(5),
        'total_questions' => 5,
        'correct_answers' => 4,
        'score' => 80,
    ]);

    MissionCompletion::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'earned_xp' => 100,
        'completed_at' => now(),
    ]);

    $response = $this
        ->actingAs($student)
        ->get(
            route('student.portfolio.index')
        );

    $response->assertOk();

    $response->assertViewHas(
        'completedMissions',
        1
    );

    $response->assertViewHas(
        'totalXp',
        100
    );

    $response->assertSee(
        'Memahami Konsep Perulangan'
    );

    $response->assertSee('Pretest');
    $response->assertSee('Posttest');

    $response->assertSee(
        'Peningkatan Nilai Pretest–Posttest'
    );

    $response->assertSee('Nilai Meningkat');

    $response->assertSeeInOrder([
        'Nilai Pretest',
        '60',
        'Nilai Posttest',
        '80',
        'Selisih Nilai',
        '+',
        '20',
    ]);
}
}
