<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\PretestAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialController extends Controller
{
    /**
     * Menampilkan halaman Materi Prakelas.
     */
    public function show(
        Request $request,
        Mission $mission
    ): View|RedirectResponse {
        abort_unless($mission->is_published, 404);

        $pretestAttempt = $mission->pretestAttempts()
    ->where('user_id', $request->user()->id)
    ->first();

if (
    $pretestAttempt?->status
    !== PretestAttempt::STATUS_COMPLETED
) {
    return redirect()
        ->route(
            'student.missions.show',
            ['mission' => $mission->slug]
        )
        ->with(
            'error',
            'Selesaikan Pretest terlebih dahulu untuk membuka Materi Prakelas.'
        );
}

        $sections = $mission->materialSections()
    ->where('is_active', true)
    ->with([
        'blocks' => function ($query) use ($request) {
            $query
                ->where('is_active', true)
                ->with([
                    'progresses' => function ($query) use ($request) {
                        $query->where(
                            'user_id',
                            $request->user()->id
                        );
                    },
                ]);
        },
    ])
    ->get();

abort_if(
    $sections->isEmpty(),
    404,
    'Materi Prakelas belum tersedia.'
);

return view('student.material.show', [
    'mission' => $mission,
    'sections' => $sections,
]);
    }
}
