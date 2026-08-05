<?php

namespace Tests\Feature\Teacher;

use App\Models\MaterialSection;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialSectionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat membuka formulir dan menambahkan
     * bagian materi baru pada sebuah misi.
     */
    public function test_teacher_can_create_material_section(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        MaterialSection::create([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' => 'pengertian-perulangan',
            'description' =>
                'Bagian materi pertama.',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.material-sections.create',
                    $mission
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.material-sections.create'
        );

        $createResponse->assertViewHas(
            'mission',
            $mission
        );

        $createResponse->assertViewHas(
            'nextSequence',
            2
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-sections.store',
                    $mission
                ),
                [
                    'title' =>
                        'Fungsi dan Ciri Perulangan',
                    'description' =>
                        'Mempelajari fungsi dan ciri struktur perulangan.',
                    'sequence' => 2,
                    'is_active' => '1',
                ]
            );

        $storeResponse->assertRedirect(
            route(
                'teacher.missions.show',
                $mission
            )
        );

        $storeResponse->assertSessionHas(
            'success',
            'Bagian materi berhasil ditambahkan.'
        );

        $this->assertDatabaseHas(
            'material_sections',
            [
                'mission_id' => $mission->id,
                'title' =>
                    'Fungsi dan Ciri Perulangan',
                'slug' =>
                    'fungsi-dan-ciri-perulangan',
                'description' =>
                    'Mempelajari fungsi dan ciri struktur perulangan.',
                'sequence' => 2,
                'is_active' => true,
            ]
        );
    }

    /**
     * Slug bagian materi dibuat tetap unik
     * ketika judul yang sama digunakan kembali.
     */
    public function test_material_section_slug_is_generated_uniquely(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        MaterialSection::create([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' => 'pengertian-perulangan',
            'description' =>
                'Bagian materi pertama.',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-sections.store',
                    $mission
                ),
                [
                    'title' =>
                        'Pengertian Perulangan',
                    'description' =>
                        'Bagian lanjutan dengan judul yang sama.',
                    'sequence' => 2,
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect(
            route(
                'teacher.missions.show',
                $mission
            )
        );

        $this->assertDatabaseHas(
            'material_sections',
            [
                'mission_id' => $mission->id,
                'title' =>
                    'Pengertian Perulangan',
                'slug' =>
                    'pengertian-perulangan-2',
                'sequence' => 2,
            ]
        );

        $this->assertSame(
            2,
            MaterialSection::query()
                ->where('mission_id', $mission->id)
                ->count()
        );
    }

    /**
     * Urutan bagian yang sudah digunakan pada misi
     * yang sama harus ditolak oleh validasi.
     */
    public function test_material_section_sequence_must_be_unique_per_mission(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        MaterialSection::create([
            'mission_id' => $mission->id,
            'title' => 'Pengertian Perulangan',
            'slug' => 'pengertian-perulangan',
            'description' =>
                'Bagian materi pertama.',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $createRoute = route(
            'teacher.missions.material-sections.create',
            $mission
        );

        $response = $this
            ->actingAs($teacher)
            ->from($createRoute)
            ->post(
                route(
                    'teacher.missions.material-sections.store',
                    $mission
                ),
                [
                    'title' =>
                        'Bagian Materi Baru',
                    'description' =>
                        'Bagian dengan urutan yang sama.',
                    'sequence' => 1,
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect($createRoute);

        $response->assertSessionHasErrors(
            'sequence'
        );

        $this->assertDatabaseMissing(
            'material_sections',
            [
                'mission_id' => $mission->id,
                'title' => 'Bagian Materi Baru',
            ]
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
     * Membuat satu misi yang dipublikasikan.
     */
    private function createMission(): Mission
    {
        return Mission::create([
            'title' =>
                'Memahami Konsep Perulangan',
            'slug' =>
                'kelola-bagian-materi-test',
            'description' =>
                'Misi pengujian pengelolaan bagian materi.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }
}
