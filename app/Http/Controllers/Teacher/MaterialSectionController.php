<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialSection;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialSectionController extends Controller
{
    /**
 * Menampilkan formulir tambah bagian materi.
 */
public function create(Mission $mission): View
{
    $nextSequence = (int) $mission
        ->materialSections()
        ->max('sequence') + 1;

    return view(
        'teacher.missions.material-sections.create',
        [
            'mission' => $mission,
            'nextSequence' => $nextSequence,
        ]
    );
}
/**
 * Menyimpan bagian materi baru pada misi.
 */
public function store(
    Request $request,
    Mission $mission
): RedirectResponse {
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
            Rule::unique(
                'material_sections',
                'sequence'
            )->where(
                fn ($query) => $query->where(
                    'mission_id',
                    $mission->id
                )
            ),
        ],
    ]);

    /*
     * Membuat slug otomatis dari judul.
     * Jika slug sudah digunakan dalam misi,
     * tambahkan nomor di belakangnya.
     */
    $baseSlug = Str::slug(
        $validated['title']
    );

    if ($baseSlug === '') {
        $baseSlug = 'bagian-materi';
    }

    $slug = $baseSlug;
    $suffix = 2;

    while (
        $mission
            ->materialSections()
            ->where('slug', $slug)
            ->exists()
    ) {
        $slug = $baseSlug . '-' . $suffix;
        $suffix++;
    }

    $mission->materialSections()->create([
        'title' => $validated['title'],
        'slug' => $slug,
        'description' =>
            $validated['description'] ?? null,
        'sequence' => $validated['sequence'],
        'is_active' =>
            $request->boolean('is_active'),
    ]);

    return redirect()
        ->route(
            'teacher.missions.show',
            $mission
        )
        ->with(
            'success',
            'Bagian materi berhasil ditambahkan.'
        );
}
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
