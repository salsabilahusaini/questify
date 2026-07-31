<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\PosttestQuestion;
use Illuminate\Database\Seeder;

class PosttestSeeder extends Seeder
{
    /**
     * Menjalankan seeder soal Posttest.
     */
    public function run(): void
    {
        $mission = Mission::where(
            'slug',
            'memahami-konsep-perulangan'
        )->firstOrFail();

        $question = PosttestQuestion::updateOrCreate(
            [
                'mission_id' => $mission->id,
                'sequence' => 1,
            ],
            [
                'question_text' =>
                    'Manakah kegiatan pemrograman berikut yang paling tepat menggunakan struktur perulangan?',
                'is_active' => true,
            ]
        );

        $options = [
            [
                'sequence' => 1,
                'option_text' =>
                    'Menampilkan bilangan genap dari 2 sampai 20 dengan pola yang sama',
                'is_correct' => true,
            ],
            [
                'sequence' => 2,
                'option_text' =>
                    'Menyimpan satu nilai bilangan ke dalam sebuah variabel',
                'is_correct' => false,
            ],
            [
                'sequence' => 3,
                'option_text' =>
                    'Memilih satu tindakan berdasarkan sebuah kondisi',
                'is_correct' => false,
            ],
            [
                'sequence' => 4,
                'option_text' =>
                    'Menghentikan program setelah satu perintah dijalankan',
                'is_correct' => false,
            ],
        ];

        foreach ($options as $option) {
            $question->options()->updateOrCreate(
                [
                    'sequence' => $option['sequence'],
                ],
                [
                    'option_text' =>
                        $option['option_text'],
                    'is_correct' =>
                        $option['is_correct'],
                ]
            );
        }
    }
}
