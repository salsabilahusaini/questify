<?php

namespace Database\Seeders;

use App\Models\FinalChallenge;
use App\Models\Mission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinalChallengeSeeder extends Seeder
{
    /**
     * Mengisi data awal Final Challenge.
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

            FinalChallenge::updateOrCreate(
                [
                    'mission_id' => $mission->id,
                ],
                [
                    'title' =>
                        'Perbaiki Program Nomor Antrean Genap',

                    'scenario' =>
                        'Sebuah sistem antrean di laboratorium komputer harus menampilkan nomor antrean genap dari 2 sampai 20. Namun, kode yang dibuat tidak menghasilkan urutan yang sesuai dan berpotensi terus berjalan tanpa berhenti. Analisis kesalahan pada kode tersebut, kemudian perbaiki agar program menghasilkan keluaran yang benar.',

                    'instructions' =>
                        'Kerjakan tantangan dengan tiga langkah: (1) perbaiki kode perulangan yang tersedia, (2) tentukan keluaran program setelah diperbaiki, dan (3) jelaskan penyebab kesalahan serta alasan perbaikannya. Gunakan struktur perulangan for.',

                    'starter_code' => <<<'CODE'
for ($i = 2; $i <= 20; $i--) {
    echo $i . " ";
}
CODE,

                    'expected_output' =>
                        '2 4 6 8 10 12 14 16 18 20',

                    'max_points' => 100,

                    'is_active' => true,
                ]
            );
        });
    }
}
