<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialSection;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialSectionController extends Controller
{
    /**
     * Menampilkan formulir edit bagian materi.
     */
    public function edit(
        Mission $mission,
        MaterialSection $section
    ): View {
        $this->ensureSectionBelongsToMission(
            $mission,
            $section
        );

        $section->loadCount([
            'blocks',
        ]);

        return view(
            'teacher.missions.material-sections.edit',
            [
                'mission' => $mission,
                'section' => $section,
            ]
        );
    }

    /**
     * Menyimpan perubahan bagian materi.
     */
    public function update(
        Request $request,
        Mission $mission,
        MaterialSection $section
    ): RedirectResponse {
        $this->ensureSectionBelongsToMission(
            $mission,
            $section
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'sequence' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        /*
         * Checkbox yang tidak dicentang tidak dikirim
         * oleh browser.
         */
        $validated['is_active'] =
            $request->boolean('is_active');

        /*
         * Mission ID dan slug tidak ikut diubah agar
         * struktur materi dan tautan tetap aman.
         */
        $section->update($validated);

        return redirect()
            ->route(
                'teacher.missions.show',
                $mission
            )
            ->with(
                'success',
                'Bagian materi berhasil diperbarui.'
            );
    }

    /**
     * Memastikan bagian materi berasal dari misi
     * yang sedang dikelola.
     */
    private function ensureSectionBelongsToMission(
        Mission $mission,
        MaterialSection $section
    ): void {
        abort_unless(
            (int) $section->mission_id
                === (int) $mission->id,
            404
        );
    }
}
