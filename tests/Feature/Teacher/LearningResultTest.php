<?php

namespace Tests\Feature\Teacher;

use App\Models\FinalChallenge;
use App\Models\FinalChallengeAttempt;
use App\Models\MicroExerciseAttempt;
use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\PosttestAttempt;
use App\Models\PretestAttempt;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningResultTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tanpa misi yang dipublikasikan, siswa tidak
     * dianggap memiliki hasil yang belum lengkap.
     */
    public function test_results_have_no_incomplete_students_without_published_mission(): void
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
                route('teacher.results.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'studentCount',
            2
        );

        $response->assertViewHas(
            'completeResultCount',
            0
        );

        $response->assertViewHas(
            'incompleteResultCount',
            0
        );

        $response->assertViewHas(
            'focusedMission',
            null
        );
    }

    /**
     * Pretest dan Posttest yang belum selesai
     * tidak boleh ikut masuk ke rekap nilai.
     */
    public function test_incomplete_pretest_and_posttest_attempts_are_ignored(): void
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
                route('teacher.results.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'completeResultCount',
            0
        );

        $response->assertViewHas(
            'incompleteResultCount',
            1
        );

        $response->assertViewHas(
            'learningResultRows',
            function ($rows) use ($student): bool {
                $row = $rows->first(
                    fn (array $item): bool =>
                        $item['student']->id
                        === $student->id
                );

                return $row !== null
                    && $row['pretest_attempt'] === null
                    && $row['posttest_attempt'] === null
                    && $row['pretest_score'] === null
                    && $row['posttest_score'] === null
                    && $row['learning_gain'] === null
                    && $row['completed_activity_count'] === 0
                    && $row['is_complete'] === false;
            }
        );
    }

    /**
     * Rekap menghitung seluruh nilai, rata-rata,
     * peningkatan nilai, XP, dan misi selesai.
     */
    public function test_complete_learning_results_are_calculated_correctly(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $student = User::factory()->create([
            'name' => 'Siswa Hasil Lengkap',
            'role' => User::ROLE_STUDENT,
        ]);

        $mission = $this->createPublishedMission();

        PretestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                PretestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(50),
            'completed_at' => now()->subMinutes(45),
            'total_questions' => 10,
            'correct_answers' => 4,
            'score' => 40,
        ]);

        MicroExerciseAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                MicroExerciseAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(40),
            'completed_at' => now()->subMinutes(35),
            'total_questions' => 10,
            'correct_answers' => 8,
            'score' => 80,
            'earned_points' => 80,
        ]);

        QuizAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                QuizAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(30),
            'completed_at' => now()->subMinutes(25),
            'total_questions' => 10,
            'correct_answers' => 7,
            'score' => 70,
            'earned_points' => 70,
        ]);

        $finalChallenge = FinalChallenge::create([
            'mission_id' => $mission->id,
            'title' =>
                'Perbaiki Program Nomor Antrean Genap',
            'scenario' =>
                'Perbaiki program agar menampilkan bilangan genap dari 2 sampai 20.',
            'instructions' =>
                'Perbaiki kode, tentukan output, dan jelaskan alasannya.',
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
            'final_challenge_id' =>
                $finalChallenge->id,
            'status' =>
                FinalChallengeAttempt::STATUS_COMPLETED,
            'answer_code' =>
                'for ($i = 2; $i <= 20; $i += 2) {}',
            'answer_output' =>
                '2 4 6 8 10 12 14 16 18 20',
            'answer_explanation' =>
                'Nilai variabel bertambah dua pada setiap perulangan.',
            'started_at' => now()->subMinutes(20),
            'completed_at' => now()->subMinutes(15),
            'score' => 90,
            'earned_points' => 90,
            'feedback' => 'Jawaban sangat baik.',
        ]);

        PosttestAttempt::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'status' =>
                PosttestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
            'total_questions' => 10,
            'correct_answers' => 7,
            'score' => 70,
        ]);

        MissionCompletion::create([
            'user_id' => $student->id,
            'mission_id' => $mission->id,
            'earned_xp' => 100,
            'completed_at' => now(),
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route('teacher.results.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'completeResultCount',
            1
        );

        $response->assertViewHas(
            'incompleteResultCount',
            0
        );

        $response->assertViewHas(
            'averagePretestScore',
            40.0
        );

        $response->assertViewHas(
            'averageMicroExerciseScore',
            80.0
        );

        $response->assertViewHas(
            'averageQuizScore',
            70.0
        );

        $response->assertViewHas(
            'averageFinalChallengeScore',
            90.0
        );

        $response->assertViewHas(
            'averagePosttestScore',
            70.0
        );

        $response->assertViewHas(
            'averageLearningGain',
            30.0
        );

        /*
         * Rata-rata lima aktivitas:
         * (40 + 80 + 70 + 90 + 70) / 5 = 70.
         */
        $response->assertViewHas(
            'overallAverageScore',
            70.0
        );

        $response->assertViewHas(
            'learningResultRows',
            function ($rows) use ($student): bool {
                $row = $rows->first(
                    fn (array $item): bool =>
                        $item['student']->id
                        === $student->id
                );

                return $row !== null
                    && $row['pretest_score'] === 40.0
                    && $row['micro_exercise_score'] === 80.0
                    && $row['quiz_score'] === 70.0
                    && $row['final_challenge_score'] === 90.0
                    && $row['posttest_score'] === 70.0
                    && $row['average_score'] === 70.0
                    && $row['learning_gain'] === 30.0
                    && $row['completed_activity_count'] === 5
                    && $row['is_complete'] === true
                    && $row['total_xp'] === 100
                    && $row['completed_missions'] === 1;
            }
        );
    }
/**
 * Siswa dengan hasil yang belum lengkap tidak
 * memengaruhi rata-rata keseluruhan.
 */
public function test_incomplete_student_does_not_affect_overall_average_score(): void
{
    $teacher = User::factory()->create([
        'role' => User::ROLE_TEACHER,
    ]);

    $completeStudent = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $incompleteStudent = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    $mission = $this->createPublishedMission();

    /*
     * Siswa pertama memiliki lima hasil lengkap,
     * seluruhnya bernilai 80.
     */
    PretestAttempt::create([
        'user_id' => $completeStudent->id,
        'mission_id' => $mission->id,
        'status' => PretestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(50),
        'completed_at' => now()->subMinutes(45),
        'total_questions' => 10,
        'correct_answers' => 8,
        'score' => 80,
    ]);

    MicroExerciseAttempt::create([
        'user_id' => $completeStudent->id,
        'mission_id' => $mission->id,
        'status' => MicroExerciseAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(40),
        'completed_at' => now()->subMinutes(35),
        'total_questions' => 10,
        'correct_answers' => 8,
        'score' => 80,
        'earned_points' => 80,
    ]);

    QuizAttempt::create([
        'user_id' => $completeStudent->id,
        'mission_id' => $mission->id,
        'status' => QuizAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now()->subMinutes(25),
        'total_questions' => 10,
        'correct_answers' => 8,
        'score' => 80,
        'earned_points' => 80,
    ]);

    $finalChallenge = FinalChallenge::create([
        'mission_id' => $mission->id,
        'title' => 'Final Challenge',
        'scenario' => 'Selesaikan permasalahan.',
        'instructions' => 'Berikan solusi.',
        'starter_code' => '',
        'expected_output' => '',
        'max_points' => 100,
        'is_active' => true,
    ]);

    FinalChallengeAttempt::create([
        'user_id' => $completeStudent->id,
        'mission_id' => $mission->id,
        'final_challenge_id' => $finalChallenge->id,
        'status' =>
            FinalChallengeAttempt::STATUS_COMPLETED,
        'answer_code' => 'jawaban',
        'answer_output' => 'hasil',
        'answer_explanation' => 'penjelasan',
        'started_at' => now()->subMinutes(20),
        'completed_at' => now()->subMinutes(15),
        'score' => 80,
        'earned_points' => 80,
        'feedback' => 'Baik.',
    ]);

    PosttestAttempt::create([
        'user_id' => $completeStudent->id,
        'mission_id' => $mission->id,
        'status' => PosttestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(10),
        'completed_at' => now()->subMinutes(5),
        'total_questions' => 10,
        'correct_answers' => 8,
        'score' => 80,
    ]);

    /*
     * Siswa kedua baru memiliki Pretest.
     * Nilai 100 ini tidak boleh mengubah
     * rata-rata keseluruhan.
     */
    PretestAttempt::create([
        'user_id' => $incompleteStudent->id,
        'mission_id' => $mission->id,
        'status' => PretestAttempt::STATUS_COMPLETED,
        'started_at' => now()->subMinutes(10),
        'completed_at' => now()->subMinutes(5),
        'total_questions' => 10,
        'correct_answers' => 10,
        'score' => 100,
    ]);

    $response = $this
        ->actingAs($teacher)
        ->get(
            route('teacher.results.index')
        );

    $response->assertOk();

    $response->assertViewHas(
        'completeResultCount',
        1
    );

    $response->assertViewHas(
        'incompleteResultCount',
        1
    );

    /*
     * Hanya siswa dengan lima hasil lengkap
     * yang masuk rata-rata keseluruhan.
     */
    $response->assertViewHas(
        'overallAverageScore',
        80.0
    );
}
    /**
     * Membuat satu misi yang dipublikasikan.
     */
    private function createPublishedMission(): Mission
    {
        return Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'hasil-perulangan-test',
            'description' =>
                'Misi pengujian hasil pembelajaran.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }
}
