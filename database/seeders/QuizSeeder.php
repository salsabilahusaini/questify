<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Mengisi data awal Kuis Materi.
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

            $questions = [
                [
                    'sequence' => 1,
                    'question' => [
                        'type' =>
                            QuizQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' =>
                            'Manakah pernyataan yang paling tepat menjelaskan perulangan dalam pemrograman?',
                        'points' => 20,
                        'explanation' =>
                            'Perulangan digunakan untuk menjalankan satu atau beberapa perintah secara berulang selama kondisi yang ditentukan masih terpenuhi.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' =>
                                'Struktur untuk memilih satu dari beberapa kondisi',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' =>
                                'Struktur untuk menjalankan perintah secara berulang berdasarkan kondisi tertentu',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' =>
                                'Struktur untuk menyimpan beberapa nilai dalam satu variabel',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' =>
                                'Struktur untuk menghentikan seluruh program secara langsung',
                            'is_correct' => false,
                        ],
                    ],
                ],
                [
                    'sequence' => 2,
                    'question' => [
                        'type' =>
                            QuizQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' =>
                            'Sebuah program harus menampilkan angka 1 sampai 100 dengan pola perintah yang sama. Alasan utama penggunaan perulangan adalah ...',
                        'points' => 20,
                        'explanation' =>
                            'Perulangan membuat program lebih ringkas, mengurangi penulisan perintah yang sama, dan memudahkan perubahan program.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' =>
                                'Menghilangkan seluruh variabel dari program',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' =>
                                'Membuat setiap keluaran selalu berbeda',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' =>
                                'Mengurangi penulisan perintah berulang sehingga kode lebih ringkas',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' =>
                                'Mencegah program menjalankan lebih dari satu perintah',
                            'is_correct' => false,
                        ],
                    ],
                ],
                [
                    'sequence' => 3,
                    'question' => [
                        'type' =>
                            QuizQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' =>
                            'Perhatikan kode berikut: for ($i = 2; $i <= 10; $i += 2). Bagian yang berfungsi mengubah nilai variabel pengontrol pada setiap pengulangan adalah ...',
                        'points' => 20,
                        'explanation' =>
                            'Bagian $i += 2 merupakan iterasi atau perubahan nilai variabel pengontrol setelah satu pengulangan selesai.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' => '$i = 2',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' => '$i <= 10',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' => '$i += 2',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' => 'for',
                            'is_correct' => false,
                        ],
                    ],
                ],
                [
                    'sequence' => 4,
                    'question' => [
                        'type' =>
                            QuizQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' =>
                            'Perhatikan kode berikut: for ($i = 1; $i <= 5; $i--) { echo $i; }. Masalah utama pada kode tersebut adalah ...',
                        'points' => 20,
                        'explanation' =>
                            'Nilai $i terus berkurang, sedangkan kondisi $i <= 5 tetap benar. Akibatnya, perulangan dapat berlangsung tanpa berhenti.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' =>
                                'Nilai awal variabel terlalu besar',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' =>
                                'Arah perubahan variabel tidak mendekati kondisi berhenti',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' =>
                                'Perintah echo tidak boleh digunakan dalam perulangan',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' =>
                                'Kondisi seharusnya selalu bernilai benar',
                            'is_correct' => false,
                        ],
                    ],
                ],
                [
                    'sequence' => 5,
                    'question' => [
                        'type' =>
                            QuizQuestion::TYPE_MULTIPLE_CHOICE,
                        'question_text' =>
                            'Perhatikan kode berikut: for ($i = 1; $i <= 7; $i += 2) { echo $i . " "; }. Output yang dihasilkan adalah ...',
                        'points' => 20,
                        'explanation' =>
                            'Nilai dimulai dari 1, bertambah 2 pada setiap pengulangan, dan berhenti setelah nilai melewati 7. Outputnya adalah 1, 3, 5, dan 7.',
                        'is_active' => true,
                    ],
                    'options' => [
                        [
                            'sequence' => 1,
                            'option_text' => '1 2 3 4 5 6 7',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 2,
                            'option_text' => '1 3 5 7',
                            'is_correct' => true,
                        ],
                        [
                            'sequence' => 3,
                            'option_text' => '2 4 6',
                            'is_correct' => false,
                        ],
                        [
                            'sequence' => 4,
                            'option_text' => '1 3 5 7 9',
                            'is_correct' => false,
                        ],
                    ],
                ],
            ];

            foreach ($questions as $questionData) {
                $question = QuizQuestion::updateOrCreate(
                    [
                        'mission_id' => $mission->id,
                        'sequence' => $questionData['sequence'],
                    ],
                    $questionData['question']
                );

                foreach (
                    $questionData['options'] as $optionData
                ) {
                    $question->options()->updateOrCreate(
                        [
                            'sequence' =>
                                $optionData['sequence'],
                        ],
                        $optionData
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

            QuizQuestion::query()
                ->where('mission_id', $mission->id)
                ->whereNotIn(
                    'sequence',
                    collect($questions)->pluck('sequence')
                )
                ->delete();
        });
    }
}
