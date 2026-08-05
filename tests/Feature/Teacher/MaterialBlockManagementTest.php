<?php

namespace Tests\Feature\Teacher;

use App\Models\MaterialBlock;
use App\Models\MaterialSection;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialBlockManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Guru dapat membuka formulir dan menambahkan
     * blok materi baru pada sebuah bagian.
     */
    public function test_teacher_can_create_material_block(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $section = $this->createSection(
            $mission
        );

        MaterialBlock::create([
            'material_section_id' => $section->id,
            'type' => MaterialBlock::TYPE_HEADING,
            'title' => 'Apa Itu Perulangan?',
            'content' => null,
            'code_language' => null,
            'media_path' => null,
            'sequence' => 1,
            'minimum_read_seconds' => 0,
            'is_required' => true,
            'is_active' => true,
        ]);

        $createResponse = $this
            ->actingAs($teacher)
            ->get(
                route(
                    'teacher.missions.material-blocks.create',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                )
            );

        $createResponse->assertOk();

        $createResponse->assertViewIs(
            'teacher.missions.material-blocks.create'
        );

        $createResponse->assertViewHas(
            'mission',
            $mission
        );

        $createResponse->assertViewHas(
            'section',
            $section
        );

        $createResponse->assertViewHas(
            'nextSequence',
            2
        );

        $storeResponse = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-blocks.store',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ),
                [
                    'type' =>
                        MaterialBlock::TYPE_PARAGRAPH,
                    'title' =>
                        'Fungsi Perulangan',
                    'content' =>
                        'Perulangan digunakan untuk menjalankan perintah secara berulang.',
                    'code_language' => 'php',
                    'media_path' =>
                        'materials/gambar-yang-tidak-digunakan.png',
                    'sequence' => 2,
                    'minimum_read_seconds' => 20,
                    'is_required' => '1',
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
            'Blok materi berhasil ditambahkan.'
        );

        $this->assertDatabaseHas(
            'material_blocks',
            [
                'material_section_id' =>
                    $section->id,
                'type' =>
                    MaterialBlock::TYPE_PARAGRAPH,
                'title' =>
                    'Fungsi Perulangan',
                'content' =>
                    'Perulangan digunakan untuk menjalankan perintah secara berulang.',
                'code_language' => null,
                'media_path' => null,
                'sequence' => 2,
                'minimum_read_seconds' => 20,
                'is_required' => true,
                'is_active' => true,
            ]
        );
    }

    /**
     * Bahasa kode disimpan hanya untuk blok
     * yang berjenis Kode Program.
     */
    public function test_code_block_stores_code_language(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $section = $this->createSection(
            $mission
        );

        $response = $this
            ->actingAs($teacher)
            ->post(
                route(
                    'teacher.missions.material-blocks.store',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ),
                [
                    'type' =>
                        MaterialBlock::TYPE_CODE,
                    'title' =>
                        'Contoh Perulangan Sederhana',
                    'content' => <<<'CODE'
for ($i = 1; $i <= 5; $i++) {
    echo $i;
}
CODE,
                    'code_language' => 'php',
                    'media_path' =>
                        'materials/gambar-yang-tidak-digunakan.png',
                    'sequence' => 1,
                    'minimum_read_seconds' => 15,
                    'is_required' => '1',
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
            'material_blocks',
            [
                'material_section_id' =>
                    $section->id,
                'type' =>
                    MaterialBlock::TYPE_CODE,
                'title' =>
                    'Contoh Perulangan Sederhana',
                'code_language' => 'php',
                'media_path' => null,
                'sequence' => 1,
                'minimum_read_seconds' => 15,
                'is_required' => true,
                'is_active' => true,
            ]
        );
    }

    /**
     * Urutan blok yang sudah digunakan dalam bagian
     * yang sama harus ditolak oleh validasi.
     */
    public function test_material_block_sequence_must_be_unique_per_section(): void
    {
        $teacher = $this->createTeacher();

        $mission = $this->createMission();

        $section = $this->createSection(
            $mission
        );

        MaterialBlock::create([
            'material_section_id' => $section->id,
            'type' =>
                MaterialBlock::TYPE_PARAGRAPH,
            'title' =>
                'Blok Materi Pertama',
            'content' =>
                'Isi blok materi pertama.',
            'code_language' => null,
            'media_path' => null,
            'sequence' => 1,
            'minimum_read_seconds' => 10,
            'is_required' => true,
            'is_active' => true,
        ]);

        $createRoute = route(
            'teacher.missions.material-blocks.create',
            [
                'mission' => $mission,
                'section' => $section,
            ]
        );

        $response = $this
            ->actingAs($teacher)
            ->from($createRoute)
            ->post(
                route(
                    'teacher.missions.material-blocks.store',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ),
                [
                    'type' =>
                        MaterialBlock::TYPE_PARAGRAPH,
                    'title' =>
                        'Blok Materi Kedua',
                    'content' =>
                        'Isi blok dengan urutan yang sama.',
                    'code_language' => null,
                    'media_path' => null,
                    'sequence' => 1,
                    'minimum_read_seconds' => 10,
                    'is_required' => '1',
                    'is_active' => '1',
                ]
            );

        $response->assertRedirect(
            $createRoute
        );

        $response->assertSessionHasErrors(
            'sequence'
        );

        $this->assertDatabaseMissing(
            'material_blocks',
            [
                'material_section_id' =>
                    $section->id,
                'title' =>
                    'Blok Materi Kedua',
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
                'kelola-blok-materi-test',
            'description' =>
                'Misi pengujian pengelolaan blok materi.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);
    }

    /**
     * Membuat satu bagian materi pada misi.
     */
    private function createSection(
        Mission $mission
    ): MaterialSection {
        return MaterialSection::create([
            'mission_id' => $mission->id,
            'title' =>
                'Pengertian Perulangan',
            'slug' =>
                'pengertian-perulangan',
            'description' =>
                'Bagian materi untuk pengujian blok.',
            'sequence' => 1,
            'is_active' => true,
        ]);
    }
}
