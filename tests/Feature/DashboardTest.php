<?php

namespace Tests\Feature;

use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\PosttestAttempt;
use App\Models\PretestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengguna yang belum login diarahkan
     * menuju halaman login.
     */
    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(
            route('student.dashboard')
        );

        $response->assertRedirect(
            route('login')
        );
    }

    /**
     * Peserta didik dapat membuka dashboard peserta didik.
     */
    public function test_authenticated_students_can_visit_the_student_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route('student.dashboard')
            );

        $response->assertOk();
    }
    /**
 * Dashboard menghitung lima dari enam tahap
 * yang selesai sebagai progres 83 persen.
 */
public function test_student_dashboard_calculates_six_mission_steps_correctly(): void
{
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = Mission::create([
        'title' => 'Memahami Konsep Perulangan',
        'slug' => 'dashboard-progress-test',
        'description' => 'Misi pengujian progres dashboard.',
        'sequence' => 1,
        'xp_reward' => 100,
        'is_published' => true,
    ]);

    PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' => PretestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now()->subMinutes(25),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
    ]);

    $materialSectionId = DB::table(
        'material_sections'
    )->insertGetId([
        'mission_id' => $mission->id,
        'title' => 'Materi Perulangan',
        'slug' => 'dashboard-material-test',
        'description' => 'Materi pengujian dashboard.',
        'sequence' => 1,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $materialBlockId = DB::table(
        'material_blocks'
    )->insertGetId([
        'material_section_id' => $materialSectionId,
        'type' => 'paragraph',
        'title' => 'Pengertian Perulangan',
        'content' => 'Materi pengujian.',
        'sequence' => 1,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    MaterialBlockProgress::create([
        'user_id' => $student->id,
        'material_block_id' => $materialBlockId,
        'first_opened_at' => now()->subMinutes(20),
        'last_activity_at' => now()->subMinutes(15),
        'total_read_seconds' => 10,
        'visit_count' => 1,
        'is_completed' => true,
        'completed_at' => now()->subMinutes(15),
    ]);

    MicroExerciseAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' => MicroExerciseAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(15),
        'completed_at' => now()->subMinutes(12),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
        'earned_points' => 10,
    ]);

    QuizAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' => QuizAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(12),
        'completed_at' => now()->subMinutes(9),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
        'earned_points' => 10,
    ]);

    $finalChallenge = FinalChallenge::create([
    'mission_id' => $mission->id,
    'title' => 'Perbaiki Program Nomor Antrean Genap',
    'scenario' =>
        'Perbaiki program agar menampilkan bilangan genap dari 2 sampai 20.',
    'instructions' =>
        'Perbaiki kode, tentukan output, dan jelaskan alasan perbaikannya.',
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

    FinalChallengeAttempt::create([
    'user_id' => $student->id,
    'mission_id' => $mission->id,
    'final_challenge_id' => $finalChallenge->id,
        'status' => FinalChallengeAttempt::STATUS_COMPLETED,
        'answer_code' => 'for ($i = 2; $i <= 20; $i += 2) {}',
        'answer_output' => '2 4 6 8 10 12 14 16 18 20',
        'answer_explanation' => 'Menampilkan bilangan genap.',
        'started_at' => now()->subMinutes(9),
        'completed_at' => now()->subMinutes(5),
        'score' => 100,
        'earned_points' => 100,
        'feedback' => 'Jawaban benar.',
    ]);

    /*
     * Posttest sengaja belum dibuat,
     * sehingga hanya lima tahap yang selesai.
     */
    $response = $this
        ->actingAs($student)
        ->get(
            route('student.dashboard')
        );

    $response->assertOk();

    $response->assertViewHas(
        'missionProgress',
        83
    );

    $response->assertViewHas(
        'finalChallengeCompleted',
        true
    );

    $response->assertViewHas(
        'posttestCompleted',
        false
    );

    $response->assertSee('83%');
    $response->assertSee('Posttest');
    $response->assertSee('Belum selesai');
}
/**
 * Siswa tidak dianggap perlu perhatian
 * ketika belum ada misi yang dipublikasikan.
 */
public function test_teacher_dashboard_has_no_attention_students_without_published_mission(): void
{
    $teacher = User::factory()->create([
        'role' => User::ROLE_TEACHER,
    ]);

    User::factory()
        ->count(2)
        ->create([
            'role' => User::ROLE_STUDENT,
        ]);

    $response = $this
        ->actingAs($teacher)
        ->get(
            route('teacher.dashboard')
        );

    $response->assertOk();

    $response->assertViewHas(
        'studentCount',
        2
    );

    $response->assertViewHas(
        'activeModuleCount',
        0
    );

    $response->assertViewHas(
        'readyStudentCount',
        0
    );

    $response->assertViewHas(
        'attentionStudentCount',
        0
    );

    $response->assertViewHas(
        'focusedMission',
        null
    );
}
    /**
     * Guru dapat membuka dashboard guru.
     */
    public function test_authenticated_teachers_can_visit_the_teacher_dashboard(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route('teacher.dashboard')
            );

        $response->assertOk();
    }
}
