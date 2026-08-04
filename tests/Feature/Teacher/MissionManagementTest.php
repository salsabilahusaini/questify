<?php

namespace Tests\Feature\Teacher;

use App\Models\Mission;
use App\Models\PosttestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Daftar misi menghitung Posttest sebagai salah
     * satu tahap pembelajaran yang dikonfigurasi.
     */
    public function test_mission_list_counts_posttest_as_configured_stage(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $this->createPosttestQuestion($mission);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route('teacher.missions.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'missionRows',
            function ($rows) use ($mission): bool {
                $row = $rows->first(
                    fn (array $item): bool =>
                        $item['mission']->id
                        === $mission->id
                );

                return $row !== null
                    && $row[
                        'posttest_question_count'
                    ] === 1
                    && $row[
                        'configured_stage_count'
                    ] === 1;
            }
        );
    }

    /**
     * Detail misi menampilkan enam tahap dan
     * menghitung persentase berdasarkan enam tahap.
     */
    public function test_mission_detail_uses_six_learning_stages(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $this->createPosttestQuestion($mission);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.show',
                    $mission
                )
            );

        $response->assertOk();

        $response->assertViewHas(
            'configuredStageCount',
            1
        );

        /*
         * Satu dari enam tahap:
         * round((1 / 6) * 100) = 17.
         */
        $response->assertViewHas(
            'configuredStagePercentage',
            17
        );

        $response->assertViewHas(
            'stages',
            function ($stages): bool {
                $posttestStage = $stages->first(
                    fn (array $stage): bool =>
                        $stage['label'] === 'Posttest'
                );

                return $stages->count() === 6
                    && $posttestStage !== null
                    && $posttestStage[
                        'configured'
                    ] === true
                    && $posttestStage[
                        'detail'
                    ] === '1 soal';
            }
        );

        $response->assertSee(
            'dari 6 tahap telah dikonfigurasi.'
        );

        $response->assertSee(
            'Kelola Soal Posttest'
        );
    }

    /**
     * Membuat akun guru untuk pengujian.
     */
    private function createTeacher(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);
    }

    /**
     * Membuat satu misi pembelajaran.
     */
    private function createMission(): Mission
    {
        return Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'kelola-misi-enam-tahap-test',
            'description' =>
                'Misi untuk menguji enam tahap pembelajaran.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu soal Posttest aktif.
     */
    private function createPosttestQuestion(
        Mission $mission
    ): PosttestQuestion {
        return PosttestQuestion::create([
            'mission_id' => $mission->id,
            'question_text' =>
                'Kegiatan manakah yang menggunakan perulangan?',
            'sequence' => 1,
            'is_active' => true,
        ]);
    }
}
