<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengguna yang belum login diarahkan
     * menuju halaman login.
     */
    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(
            route('student.dashboard')
        );

        $response->assertRedirect(
            route('login')
        );
    }

    /**
     * Peserta didik dapat membuka dashboard peserta didik.
     */
    public function test_authenticated_students_can_visit_the_student_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $response = $this
            ->actingAs($student)
            ->get(
                route('student.dashboard')
            );

        $response->assertOk();
    }

    /**
     * Guru dapat membuka dashboard guru.
     */
    public function test_authenticated_teachers_can_visit_the_teacher_dashboard(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(
                route('teacher.dashboard')
            );

        $response->assertOk();
    }
}
