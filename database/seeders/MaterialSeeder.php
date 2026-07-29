<?php

namespace Database\Seeders;

use App\Models\MaterialBlock;
use App\Models\MaterialSection;
use App\Models\Mission;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Mengisi data awal Materi Prakelas.
     */
    public function run(): void
    {
        $mission = Mission::where(
            'slug',
            'memahami-konsep-perulangan'
        )->firstOrFail();

        $section = MaterialSection::updateOrCreate(
            [
                'mission_id' => $mission->id,
                'slug' => 'pengertian-perulangan',
            ],
            [
                'title' => 'Pengertian Perulangan',
                'description' => 'Memahami konsep dasar perulangan dalam pemrograman.',
                'sequence' => 1,
                'is_active' => true,
            ]
        );

        $blocks = [
            [
                'sequence' => 1,
                'type' => MaterialBlock::TYPE_HEADING,
                'title' => 'Apa Itu Perulangan?',
                'content' => null,
                'code_language' => null,
                'media_path' => null,
                'minimum_read_seconds' => 0,
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'sequence' => 2,
                'type' => MaterialBlock::TYPE_PARAGRAPH,
                'title' => null,
                'content' => 'Perulangan adalah struktur dalam pemrograman yang digunakan untuk menjalankan serangkaian perintah secara berulang selama syarat tertentu masih terpenuhi.',
                'code_language' => null,
                'media_path' => null,
                'minimum_read_seconds' => 20,
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'sequence' => 3,
                'type' => MaterialBlock::TYPE_PARAGRAPH,
                'title' => null,
                'content' => 'Dengan menggunakan perulangan, programmer tidak perlu menuliskan perintah yang sama berkali-kali. Program menjadi lebih ringkas, terstruktur, dan mudah dikembangkan.',
                'code_language' => null,
                'media_path' => null,
                'minimum_read_seconds' => 20,
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'sequence' => 4,
                'type' => MaterialBlock::TYPE_CODE,
                'title' => 'Contoh Perulangan Sederhana',
                'content' => <<<'CODE'
for ($i = 1; $i <= 5; $i++) {
    echo $i . PHP_EOL;
}
CODE,
                'code_language' => 'php',
                'media_path' => null,
                'minimum_read_seconds' => 15,
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'sequence' => 5,
                'type' => MaterialBlock::TYPE_PARAGRAPH,
                'title' => null,
                'content' => 'Kode tersebut menjalankan perintah secara berulang mulai dari nilai 1 sampai 5. Setiap pengulangan menampilkan nilai variabel $i.',
                'code_language' => null,
                'media_path' => null,
                'minimum_read_seconds' => 15,
                'is_required' => true,
                'is_active' => true,
            ],
        ];

        foreach ($blocks as $block) {
            MaterialBlock::updateOrCreate(
                [
                    'material_section_id' => $section->id,
                    'sequence' => $block['sequence'],
                ],
                $block
            );
        }
    }
}
