<?php

namespace Database\Seeders;

use App\Models\Mission;
use App\Models\PretestQuestion;
use Illuminate\Database\Seeder;

class PretestSeeder extends Seeder
{
    /**
     * Menjalankan seeder soal Pretest.
     */
    public function run(): void
    {
        $mission = Mission::where(
            'slug',
            'memahami-konsep-perulangan'
        )->firstOrFail();

        $question = PretestQuestion::updateOrCreate(
            [
                'mission_id' => $mission->id,
                'sequence' => 1,
            ],
            [
                'question_text' => 'Manakah aktivitas berikut yang paling tepat menggambarkan konsep perulangan?',
                'is_active' => true,
            ]
        );

        $options = [
            [
                'sequence' => 1,
                'option_text' => 'Memasukkan kata sandi satu kali untuk masuk ke sistem',
                'is_correct' => false,
            ],
            [
                'sequence' => 2,
                'option_text' => 'Mencetak angka 1 sampai 10 menggunakan pola yang sama',
                'is_correct' => true,
            ],
            [
                'sequence' => 3,
                'option_text' => 'Memilih salah satu menu pada halaman utama',
                'is_correct' => false,
            ],
            [
                'sequence' => 4,
                'option_text' => 'Menyimpan sebuah nama ke dalam satu variabel',
                'is_correct' => false,
            ],
        ];

        foreach ($options as $option) {
            $question->options()->updateOrCreate(
                [
                    'sequence' => $option['sequence'],
                ],
                [
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]
            );
        }
    }
}
