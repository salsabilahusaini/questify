<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Mengisi data awal aplikasi Questify.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@questify.test',
            ],
            [
                'name' => 'Admin Questify',
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'guru@questify.test',
            ],
            [
                'name' => 'Guru Questify',
                'role' => User::ROLE_TEACHER,
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'siswa@questify.test',
            ],
            [
                'name' => 'Siswa Questify',
                'role' => User::ROLE_STUDENT,
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );

        $this->call([
            MissionSeeder::class,
            PretestSeeder::class,
            MaterialSeeder::class,
            MicroExerciseSeeder::class,
        ]);
    }
}
