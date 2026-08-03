<?php

namespace Tests\Feature\Teacher;

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

class StudentMonitoringTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tanpa misi yang dipublikasikan, siswa tidak
     * langsung dianggap memerlukan perhatian.
     */
    public function test_monitoring_has_no_attention_students_without_published_mission(): void
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
                route('teacher.monitoring.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'studentCount',
            2
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
     * Monitoring menghitung siswa siap dan perlu
     * perhatian berdasarkan empat kegiatan prakelas.
     */
    public function test_monitoring_calculates_student_readiness_correctly(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $readyStudent = User::factory()->create([
            'name' => 'Siswa Siap',
            'role' => User::ROLE_STUDENT,
        ]);

        $attentionStudent = User::factory()->create([
            'name' => 'Siswa Perlu Perhatian',
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createPublishedMission();

        $materialBlockId =
            $this->createRequiredMaterialBlock(
                $mission
            );

        PretestAttempt::create([
            'user_id' => $readyStudent->id,
            'mission_id' => $mission->id,
            'status' =>
                PretestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now()->subMinutes(25),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
        ]);

        MaterialBlockProgress::create([
            'user_id' => $readyStudent->id,
            'material_block_id' => $materialBlockId,
            'first_opened_at' => now()->subMinutes(20),
            'last_activity_at' => now()->subMinutes(15),
            'total_read_seconds' => 65,
            'visit_count' => 2,
            'is_completed' => true,
            'completed_at' => now()->subMinutes(15),
        ]);

        MicroExerciseAttempt::create([
            'user_id' => $readyStudent->id,
            'mission_id' => $mission->id,
            'status' =>
                MicroExerciseAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(15),
            'completed_at' => now()->subMinutes(12),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
            'earned_points' => 10,
        ]);

        QuizAttempt::create([
            'user_id' => $readyStudent->id,
            'mission_id' => $mission->id,
            'status' =>
                QuizAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(12),
            'completed_at' => now()->subMinutes(9),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
            'earned_points' => 20,
        ]);

        /*
         * Attempt ini memiliki completed_at, tetapi
         * statusnya belum selesai sehingga harus diabaikan.
         */
        PretestAttempt::create([
            'user_id' => $attentionStudent->id,
            'mission_id' => $mission->id,
            'status' =>
                PretestAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 1,
            'correct_answers' => 0,
            'score' => 0,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route('teacher.monitoring.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'studentCount',
            2
        );

        $response->assertViewHas(
            'readyStudentCount',
            1
        );

        $response->assertViewHas(
            'attentionStudentCount',
            1
        );

        $response->assertViewHas(
            'requiredMaterialBlockCount',
            1
        );

        $response->assertViewHas(
            'studentMonitoringRows',
            function ($rows) use (
                $readyStudent,
                $attentionStudent
            ): bool {
                $readyRow = $rows->first(
                    fn (array $row): bool =>
                        $row['student']->id
                        === $readyStudent->id
                );

                $attentionRow = $rows->first(
                    fn (array $row): bool =>
                        $row['student']->id
                        === $attentionStudent->id
                );

                return $readyRow !== null
                    && $readyRow['is_ready'] === true
                    && $readyRow['pretest_completed']
                        === true
                    && $readyRow['material_completed']
                        === true
                    && $readyRow[
                        'micro_exercise_completed'
                    ] === true
                    && $readyRow['quiz_completed']
                        === true
                    && $readyRow['total_read_seconds']
                        === 65
                    && $readyRow['formatted_read_time']
                        === '1 menit 5 detik'
                    && $readyRow['total_visits'] === 2
                    && $attentionRow !== null
                    && $attentionRow['is_ready']
                        === false
                    && $attentionRow[
                        'pretest_completed'
                    ] === false;
            }
        );
    }

    /**
     * Detail siswa hanya menampilkan Pretest dan
     * Posttest yang benar-benar berstatus selesai.
     */
    public function test_detail_ignores_incomplete_pretest_and_posttest_attempts(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createPublishedMission();

        PretestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                PretestAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(20),
            'completed_at' => now()->subMinutes(15),
            'total_questions' => 1,
            'correct_answers' => 0,
            'score' => 0,
        ]);

        PosttestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                PosttestAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 1,
            'correct_answers' => 0,
            'score' => 0,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.monitoring.show',
                    [
                        'student' => $student->id,
                    ]
                )
            );

        $response->assertOk();

        $response->assertViewHas(
            'pretestAttempt',
            null
        );

        $response->assertViewHas(
            'posttestAttempt',
            null
        );

        $response->assertViewHas(
            'learningGain',
            null
        );

        $response->assertViewHas(
            'isReady',
            false
        );
    }

    /**
     * Detail monitoring tidak boleh digunakan
     * untuk membuka akun selain peserta didik.
     */
    public function test_monitoring_detail_rejects_non_student_account(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $otherTeacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.monitoring.show',
                    [
                        'student' => $otherTeacher->id,
                    ]
                )
            );

        $response->assertNotFound();
    }

    /**
     * Membuat satu misi yang dipublikasikan.
     */
    private function createPublishedMission(): Mission
    {
        return Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'monitoring-perulangan-test',
            'description' =>
                'Misi pengujian monitoring siswa.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu blok materi wajib untuk misi.
     */
    private function createRequiredMaterialBlock(
        Mission $mission
    ): int {
        $materialSectionId = DB::table(
            'material_sections'
        )->insertGetId([
            'mission_id' => $mission->id,
            'title' => 'Materi Perulangan',
            'slug' => 'monitoring-material-test',
            'description' =>
                'Materi pengujian monitoring.',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table(
            'material_blocks'
        )->insertGetId([
            'material_section_id' =>
                $materialSectionId,
            'type' => 'paragraph',
            'title' => 'Pengertian Perulangan',
            'content' => 'Materi pengujian.',
            'sequence' => 1,
            'minimum_read_seconds' => 60,
            'is_required' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
