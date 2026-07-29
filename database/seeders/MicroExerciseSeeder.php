<?php

namespace Database\Seeders;

use App\Models\MaterialBlock;
use App\Models\MaterialSection;
use App\Models\MicroExerciseQuestion;
use App\Models\Mission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MicroExerciseSeeder extends Seeder
{
    /**
     * Mengisi data awal Latihan Mikro.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $mission = Mission::query()
                ->where(
                    'slug',
                    'memahami-konsep-perulangan'
                )
                ->firstOrFail();

            $section = MaterialSection::query()
                ->where('mission_id', $mission->id)
                ->where('slug', 'pengertian-perulangan')
                ->firstOrFail();

            $blocks = MaterialBlock::query()
                ->where(
                    'material_section_id',
                    $section->id
                )
                ->where('is_active', true)
                ->get()
                ->keyBy('sequence');

            $questions = [
                [
                    'block_sequence' => 2,
                    'question' => [
                        'type' => MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' => 'Sebuah program harus menampilkan teks yang sama sebanyak 20 kali. Cara yang paling tepat untuk menyelesaikannya adalah ...',
                        'sequence' => 1,
                        'points' => 10,
                        'correct_feedback' => 'Tepat. Perulangan dapat menjalankan perintah yang sama berkali-kali secara lebih ringkas.',
                        'incorrect_feedback' => 'Belum tepat. Perhatikan cara menjalankan perintah yang sama secara otomatis tanpa menulisnya berulang kali.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' => 'Menulis perintah yang sama sebanyak 20 baris',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' => 'Menggunakan perulangan dengan batas 20 kali',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' => 'Menyimpan teks ke dalam sebuah variabel saja',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' => 'Menggunakan satu kondisi percabangan',
                            'is_correct' => false,
                        ],
                    ],
                ],
                [
                    'block_sequence' => 3,
                    'question' => [
                        'type' => MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' => 'Program A menuliskan sepuluh perintah cetak secara terpisah, sedangkan Program B menggunakan satu struktur perulangan. Keuntungan utama Program B adalah ...',
                        'sequence' => 1,
                        'points' => 10,
                        'correct_feedback' => 'Benar. Perulangan membuat kode lebih ringkas, terstruktur, dan mudah diubah.',
                        'incorrect_feedback' => 'Belum tepat. Bandingkan jumlah penulisan perintah dan kemudahan perubahan pada kedua program.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' => 'Program selalu menghasilkan keluaran yang berbeda',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' => 'Kode lebih ringkas dan mudah dikembangkan',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' => 'Program tidak memerlukan variabel',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' => 'Semua perintah dijalankan hanya satu kali',
                            'is_correct' => false,
                        ],
                    ],
                ],
                [
                    'block_sequence' => 4,
                    'question' => [
                        'type' => MicroExerciseQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' => 'Perhatikan kode berikut: for ($i = 1; $i <= 5; $i++) { echo $i . PHP_EOL; } Urutan output yang dihasilkan adalah ...',
                        'sequence' => 1,
                        'points' => 10,
                        'correct_feedback' => 'Tepat. Nilai dimulai dari 1, bertambah satu, dan berhenti setelah mencapai 5.',
                        'incorrect_feedback' => 'Belum tepat. Periksa nilai awal, kondisi batas, dan perubahan nilai variabel $i.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' => '1, 2, 3, 4, 5',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' => '0, 1, 2, 3, 4',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' => '1, 2, 3, 4, 5, 6',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' => '5, 4, 3, 2, 1',
                            'is_correct' => false,
                        ],
                    ],
                ],
            ];

            foreach ($questions as $questionData) {
                $block = $blocks->get(
                    $questionData['block_sequence']
                );

                if (! $block) {
                    throw new \RuntimeException(
                        'Blok materi untuk Latihan Mikro tidak ditemukan.'
                    );
                }

                $question = MicroExerciseQuestion::updateOrCreate(
                    [
                        'material_block_id' => $block->id,
                        'sequence' => $questionData['question']['sequence'],
                    ],
                    $questionData['question']
                );

                foreach ($questionData['options'] as $option) {
                    $question->options()->updateOrCreate(
                        [
                            'sequence' => $option['sequence'],
                        ],
                        $option
                    );
                }

                $question->options()
                    ->whereNotIn(
                        'sequence',
                        collect($questionData['options'])
                            ->pluck('sequence')
                    )
                    ->delete();
            }
        });
    }
}
