<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard guru.
     */
    public function __invoke(): View
    {
        /*
         * Untuk sementara, jumlah peserta didik dihitung dari seluruh
         * akun student yang terdaftar. Relasi guru dan kelas akan
         * ditambahkan ketika fitur kelas dikembangkan.
         */
        $studentCount = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->count();

        return view('teacher.dashboard', [
            'studentCount' => $studentCount,

            /*
             * Nilai berikut masih 0 karena tabel modul dan aktivitas
             * pembelajaran belum dibuat.
             */
            'activeModuleCount' => 0,
            'readyStudentCount' => 0,
            'attentionStudentCount' => 0,
        ]);
    }
}
