<?php

namespace Tests\Feature\Student;

use App\Http\Controllers\Student\MaterialController;
use App\Models\MaterialBlockProgress;
use App\Models\Mission;
use App\Models\PretestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MaterialProgressTest extends TestCase
{
    use RefreshDatabase;

    private User $student;

    private Mission $mission;

    private int $materialBlockId;

    /**
     * Menyiapkan siswa, misi, bagian materi,
     * dan satu blok materi wajib.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $this->mission = Mission::create([
            'title' => 'Memahami Konsep Perulangan',
            'slug' => 'materi-perulangan-test',
            'description' => 'Misi pengujian Materi Prakelas.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $materialSectionId = DB::table(
            'material_sections'
        )->insertGetId([
            'mission_id' => $this->mission->id,
            'title' => 'Materi Perulangan',
            'slug' => 'bagian-materi-perulangan-test',
            'description' => 'Bagian materi pengujian.',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->materialBlockId = DB::table(
            'material_blocks'
        )->insertGetId([
            'material_section_id' => $materialSectionId,
            'type' => 'paragraph',
            'title' => 'Pengertian Perulangan',
            'content' => 'Materi untuk pengujian monitoring baca.',
            'sequence' => 1,
            'minimum_read_seconds' => 10,
            'is_required' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Halaman Materi Prakelas tidak dapat dibuka
     * sebelum siswa menyelesaikan Pretest.
     */
    public function test_material_page_is_rejected_before_pretest_is_completed(): void
    {
        $response = $this
            ->actingAs($this->student)
            ->get(
                action(
                    [
                        MaterialController::class,
                        'show',
                    ],
                    [
                        'mission' => $this->mission,
                    ]
                )
            );

        $response->assertRedirect(
            route(
                'student.missions.show',
                [
                    'mission' => $this->mission->slug,
                ]
            )
        );

        $response->assertSessionHas(
            'error',
            'Selesaikan Pretest terlebih dahulu untuk membuka Materi Prakelas.'
        );

        $this->assertDatabaseCount(
            'material_block_progresses',
            0
        );
    }

    /**
     * Endpoint pembukaan dan heartbeat ditolak
     * sebelum siswa menyelesaikan Pretest.
     */
    public function test_progress_endpoints_are_rejected_before_pretest_is_completed(): void
    {
        $openResponse = $this
            ->actingAs($this->student)
            ->postJson(
                route(
                    'student.missions.material.blocks.open',
                    [
                        'mission' => $this->mission->slug,
                        'block' => $this->materialBlockId,
                    ]
                )
            );

        $openResponse->assertForbidden();

        $heartbeatResponse = $this
            ->actingAs($this->student)
            ->postJson(
                route(
                    'student.missions.material.blocks.heartbeat',
                    [
                        'mission' => $this->mission->slug,
                        'block' => $this->materialBlockId,
                    ]
                ),
                [
                    'seconds' => 5,
                ]
            );

        $heartbeatResponse->assertForbidden();

        $this->assertDatabaseCount(
            'material_block_progresses',
            0
        );
    }

    /**
     * Halaman Materi Prakelas dapat dibuka
     * setelah siswa menyelesaikan Pretest.
     */
    public function test_material_page_can_be_opened_after_pretest_is_completed(): void
    {
        $this->completePretest();

        $response = $this
            ->actingAs($this->student)
            ->get(
                action(
                    [
                        MaterialController::class,
                        'show',
                    ],
                    [
                        'mission' => $this->mission,
                    ]
                )
            );

        $response->assertOk();

        $response->assertSee(
            'Memahami Konsep Perulangan'
        );

        $response->assertSee(
            'Pengertian Perulangan'
        );

        $response->assertSee(
            '10 detik'
        );
    }

    /**
     * Pembukaan blok membuat progress
     * dan menambah jumlah kunjungan.
     */
    public function test_open_creates_progress_and_increments_visit_count(): void
    {
        $this->completePretest();

        $openRoute = route(
            'student.missions.material.blocks.open',
            [
                'mission' => $this->mission->slug,
                'block' => $this->materialBlockId,
            ]
        );

        $firstResponse = $this
            ->actingAs($this->student)
            ->postJson($openRoute);

        $firstResponse->assertOk();

        $firstResponse->assertJsonPath(
            'total_read_seconds',
            0
        );

        $firstResponse->assertJsonPath(
            'minimum_read_seconds',
            10
        );

        $firstResponse->assertJsonPath(
            'is_completed',
            false
        );

        $this->assertDatabaseHas(
            'material_block_progresses',
            [
                'user_id' => $this->student->id,
                'material_block_id' =>
                    $this->materialBlockId,
                'total_read_seconds' => 0,
                'visit_count' => 1,
                'is_completed' => false,
            ]
        );

        $secondResponse = $this
            ->actingAs($this->student)
            ->postJson($openRoute);

        $secondResponse->assertOk();

        $this->assertDatabaseHas(
            'material_block_progresses',
            [
                'user_id' => $this->student->id,
                'material_block_id' =>
                    $this->materialBlockId,
                'visit_count' => 2,
            ]
        );

        $progressCount = MaterialBlockProgress::query()
            ->where('user_id', $this->student->id)
            ->where(
                'material_block_id',
                $this->materialBlockId
            )
            ->count();

        $this->assertSame(
            1,
            $progressCount
        );
    }

    /**
     * Heartbeat menambahkan durasi baca
     * dan menyelesaikan blok saat batas tercapai.
     */
    public function test_heartbeat_adds_read_time_and_completes_block(): void
    {
        $this->completePretest();

        $this
            ->actingAs($this->student)
            ->postJson(
                route(
                    'student.missions.material.blocks.open',
                    [
                        'mission' => $this->mission->slug,
                        'block' => $this->materialBlockId,
                    ]
                )
            )
            ->assertOk();

        $heartbeatRoute = route(
            'student.missions.material.blocks.heartbeat',
            [
                'mission' => $this->mission->slug,
                'block' => $this->materialBlockId,
            ]
        );

        $firstHeartbeat = $this
            ->actingAs($this->student)
            ->postJson(
                $heartbeatRoute,
                [
                    'seconds' => 5,
                ]
            );

        $firstHeartbeat->assertOk();

        $firstHeartbeat->assertJsonPath(
            'total_read_seconds',
            5
        );

        $firstHeartbeat->assertJsonPath(
            'is_completed',
            false
        );

        $secondHeartbeat = $this
            ->actingAs($this->student)
            ->postJson(
                $heartbeatRoute,
                [
                    'seconds' => 5,
                ]
            );

        $secondHeartbeat->assertOk();

        $secondHeartbeat->assertJsonPath(
            'total_read_seconds',
            10
        );

        $secondHeartbeat->assertJsonPath(
            'is_completed',
            true
        );

        $progress = MaterialBlockProgress::query()
            ->where('user_id', $this->student->id)
            ->where(
                'material_block_id',
                $this->materialBlockId
            )
            ->firstOrFail();

        $this->assertSame(
            10,
            $progress->total_read_seconds
        );

        $this->assertTrue(
            $progress->is_completed
        );

        $this->assertNotNull(
            $progress->completed_at
        );
    }

    /**
     * Blok dari misi lain tidak dapat dicatat
     * menggunakan alamat misi yang berbeda.
     */
    public function test_block_from_another_mission_cannot_be_tracked(): void
    {
        $this->completePretest();

        $otherMission = Mission::create([
            'title' => 'Misi Array',
            'slug' => 'misi-array-material-test',
            'description' => 'Misi lain untuk pengujian.',
            'sequence' => 2,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        $otherSectionId = DB::table(
            'material_sections'
        )->insertGetId([
            'mission_id' => $otherMission->id,
            'title' => 'Materi Array',
            'slug' => 'bagian-materi-array-test',
            'description' => 'Bagian materi misi lain.',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $otherBlockId = DB::table(
            'material_blocks'
        )->insertGetId([
            'material_section_id' => $otherSectionId,
            'type' => 'paragraph',
            'title' => 'Pengertian Array',
            'content' => 'Materi dari misi yang berbeda.',
            'sequence' => 1,
            'minimum_read_seconds' => 10,
            'is_required' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($this->student)
            ->postJson(
                route(
                    'student.missions.material.blocks.open',
                    [
                        'mission' => $this->mission->slug,
                        'block' => $otherBlockId,
                    ]
                )
            );

        $response->assertNotFound();

        $this->assertDatabaseMissing(
            'material_block_progresses',
            [
                'user_id' => $this->student->id,
                'material_block_id' => $otherBlockId,
            ]
        );
    }

    /**
     * Menandai Pretest sebagai selesai untuk siswa.
     */
    private function completePretest(): void
    {
        PretestAttempt::create([
            'user_id' => $this->student->id,
            'mission_id' => $this->mission->id,
            'status' => PretestAttempt::STATUS_COMPLETED,
            'started_at' => now()->subMinutes(15),
            'completed_at' => now()->subMinutes(10),
            'total_questions' => 1,
            'correct_answers' => 1,
            'score' => 100,
        ]);
    }
}
