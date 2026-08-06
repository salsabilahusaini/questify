<?php

namespace Tests\Feature\Admin;

use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dashboard Admin menampilkan statistik
     * pengguna dan misi berdasarkan data nyata.
     */
    public function test_admin_dashboard_displays_real_platform_statistics(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        User::factory()->count(2)->create([
            'role' => User::ROLE_STUDENT,
        ]);

        Mission::create([
            'title' => 'Misi Perulangan',
            'slug' => 'misi-perulangan-admin-dashboard-test',
            'description' =>
                'Misi untuk pengujian Dashboard Admin.',
            'sequence' => 1,
            'xp_reward' => 100,
            'is_published' => true,
        ]);

        Mission::create([
            'title' => 'Misi Array',
            'slug' => 'misi-array-admin-dashboard-test',
            'description' =>
                'Misi draft untuk pengujian Dashboard Admin.',
            'sequence' => 2,
            'xp_reward' => 100,
            'is_published' => false,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertOk();

        $response->assertViewHas([
            'totalUsers' => 4,
            'totalTeachers' => 1,
            'totalStudents' => 2,
            'totalMissions' => 2,
        ]);

        $response->assertSeeText('Total Pengguna');
        $response->assertSeeText('Jumlah Guru');
        $response->assertSeeText('Peserta Didik');
        $response->assertSeeText('Misi Pembelajaran');
    }
}
