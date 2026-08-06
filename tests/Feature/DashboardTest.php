<?php

namespace Tests\Feature;

use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\Badge;
use App\Models\MaterialBlockProgress;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PosttestAttempt;
use App\Models\PretestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
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
 * Dashboard menampilkan progres, XP, level,
 * misi selesai, dan badge berdasarkan data siswa.
 */
public function test_student_dashboard_displays_completed_mission_statistics(): void
{
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = Mission::create([
        'title' => 'Memahami Konsep Perulangan',
        'slug' => 'dashboard-completed-mission-test',
        'description' =>
            'Misi yang seluruh tahapnya telah selesai.',
        'sequence' => 1,
        'xp_reward' => 100,
        'is_published' => true,
    ]);

    PretestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PretestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(60),
        'completed_at' => now()->subMinutes(55),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
    ]);

    $materialSectionId = DB::table(
        'material_sections'
    )->insertGetId([
        'mission_id' => $mission->id,
        'title' => 'Materi Perulangan',
        'slug' => 'dashboard-completed-material',
        'description' =>
            'Materi pengujian dashboard selesai.',
        'sequence' => 1,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $materialBlockId = DB::table(
        'material_blocks'
    )->insertGetId([
        'material_section_id' =>
            $materialSectionId,
        'type' => 'paragraph',
        'title' => 'Pengertian Perulangan',
        'content' =>
            'Materi yang telah dibaca siswa.',
        'sequence' => 1,
        'minimum_read_seconds' => 10,
        'is_required' => true,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    MaterialBlockProgress::create([
        'user_id' => $student->id,
        'material_block_id' =>
            $materialBlockId,
        'first_opened_at' =>
            now()->subMinutes(50),
        'last_activity_at' =>
            now()->subMinutes(45),
        'total_read_seconds' => 120,
        'visit_count' => 2,
        'is_completed' => true,
        'completed_at' =>
            now()->subMinutes(45),
    ]);

    MicroExerciseAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            MicroExerciseAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(40),
        'completed_at' => now()->subMinutes(35),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
        'earned_points' => 10,
    ]);

    QuizAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            QuizAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now()->subMinutes(25),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
        'earned_points' => 10,
    ]);

    $finalChallenge = FinalChallenge::create([
        'mission_id' => $mission->id,
        'title' => 'Final Challenge Perulangan',
        'scenario' =>
            'Selesaikan permasalahan perulangan.',
        'instructions' =>
            'Perbaiki kode dan jelaskan hasilnya.',
        'starter_code' =>
            'for ($i = 1; $i <= 5; $i++) {}',
        'expected_output' => '1 2 3 4 5',
        'max_points' => 100,
        'is_active' => true,
    ]);

    FinalChallengeAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'final_challenge_id' =>
            $finalChallenge->id,
        'status' =>
            FinalChallengeAttempt::STATUS_COMPLETED,
        'answer_code' =>
            'for ($i = 1; $i <= 5; $i++) {}',
        'answer_output' => '1 2 3 4 5',
        'answer_explanation' =>
            'Program menampilkan angka satu sampai lima.',
        'started_at' => now()->subMinutes(20),
        'completed_at' => now()->subMinutes(15),
        'score' => 100,
        'earned_points' => 100,
        'feedback' => 'Jawaban benar.',
    ]);

    PosttestAttempt::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'status' =>
            PosttestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(10),
        'completed_at' => now()->subMinutes(5),
        'total_questions' => 1,
        'correct_answers' => 1,
        'score' => 100,
    ]);

    MissionCompletion::create([
        'user_id' => $student->id,
        'mission_id' => $mission->id,
        'earned_xp' => 100,
        'completed_at' => now(),
    ]);

    $badge = Badge::create([
        'name' => 'Misi Pertama',
        'slug' =>
            'dashboard-misi-pertama-test',
        'description' =>
            'Diperoleh setelah menyelesaikan satu misi.',
        'icon' => 'flag',
        'theme' => 'blue',
        'criteria_type' =>
            Badge::CRITERIA_MISSIONS_COMPLETED,
        'criteria_value' => 1,
        'sequence' => 1,
        'is_active' => true,
    ]);

    UserBadge::create([
        'user_id' => $student->id,
        'badge_id' => $badge->id,
        'awarded_at' => now(),
    ]);

    $response = $this
        ->actingAs($student)
        ->get(
            route('student.dashboard')
        );

    $response->assertOk();

    $response->assertViewHas(
        'completedMissionCount',
        1
    );

    $response->assertViewHas(
        'totalXp',
        100
    );

    $response->assertViewHas(
        'currentLevel',
        'Beginner'
    );

    $response->assertViewHas(
        'badgeCount',
        1
    );

    $response->assertViewHas(
        'missionProgress',
        100
    );

    $response->assertViewHas(
        'pretestCompleted',
        true
    );

    $response->assertViewHas(
        'materialCompleted',
        true
    );

    $response->assertViewHas(
        'microPracticeCompleted',
        true
    );

    $response->assertViewHas(
        'quizCompleted',
        true
    );

    $response->assertViewHas(
        'finalChallengeCompleted',
        true
    );

    $response->assertViewHas(
        'posttestCompleted',
        true
    );

    $response->assertSee('100%');
    $response->assertSee('Beginner');
    $response->assertSee('Selamat! Kamu telah menyelesaikan seluruh tahapan misi ini.');
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
