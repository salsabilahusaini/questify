<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialSection;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    $requestedIsActive =
    $request->boolean('is_active');

/*
 * Setelah peserta didik mulai beraktivitas pada
 * Materi Prakelas, Bagian Materi baru tidak boleh
 * langsung ditambahkan dalam keadaan aktif karena
 * dapat mengubah struktur pembelajaran siswa.
 */
if ($requestedIsActive) {
    $hasStudentActivity = DB::table(
        'material_block_progresses'
    )
        ->join(
            'material_blocks',
            'material_blocks.id',
            '=',
            'material_block_progresses.material_block_id'
        )
        ->join(
            'material_sections',
            'material_sections.id',
            '=',
            'material_blocks.material_section_id'
        )
        ->where(
            'material_sections.mission_id',
            $mission->id
        )
        ->exists();

    if ($hasStudentActivity) {
        return redirect()
            ->route(
                'teacher.missions.material-sections.create',
                $mission
            )
            ->withInput()
            ->with(
                'error',
                'Bagian materi aktif tidak dapat ditambahkan karena materi sudah memiliki aktivitas peserta didik.'
            );
    }
}

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
        'is_active' => $requestedIsActive,
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
    Rule::unique(
        'material_sections',
        'sequence'
    )
        ->where(
            fn ($query) => $query->where(
                'mission_id',
                $mission->id
            )
        )
        ->ignore($section->id),
],
        ]);

        /*
         * Checkbox yang tidak dicentang tidak dikirim
         * oleh browser.
         */
        $requestedIsActive =
    $request->boolean('is_active');

/*
 * Setelah peserta didik mulai beraktivitas pada
 * Materi Prakelas, status aktif Bagian Materi
 * tidak boleh diubah karena dapat mengubah
 * struktur pembelajaran yang sedang berjalan.
 */
if (
    $requestedIsActive !== (bool) $section->is_active
) {
    $hasStudentActivity = DB::table(
        'material_block_progresses'
    )
        ->join(
            'material_blocks',
            'material_blocks.id',
            '=',
            'material_block_progresses.material_block_id'
        )
        ->join(
            'material_sections',
            'material_sections.id',
            '=',
            'material_blocks.material_section_id'
        )
        ->where(
            'material_sections.mission_id',
            $mission->id
        )
        ->exists();

    if ($hasStudentActivity) {
        return redirect()
            ->route(
                'teacher.missions.material-sections.edit',
                [
                    'mission' => $mission,
                    'section' => $section,
                ]
            )
            ->with(
                'error',
                'Status aktif bagian materi tidak dapat diubah karena materi sudah memiliki aktivitas peserta didik.'
            );
    }
}

$validated['is_active'] =
    $requestedIsActive;

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
 * Menghapus bagian materi yang belum memiliki
 * riwayat aktivitas peserta didik.
 */
public function destroy(
    Mission $mission,
    MaterialSection $section
): RedirectResponse {
    $this->ensureSectionBelongsToMission(
        $mission,
        $section
    );

    /*
     * Riwayat waktu baca, jumlah kunjungan,
     * dan penyelesaian seluruh blok harus dilindungi.
     */
    $hasStudentProgress = DB::table(
        'material_block_progresses'
    )
        ->join(
            'material_blocks',
            'material_blocks.id',
            '=',
            'material_block_progresses.material_block_id'
        )
        ->where(
            'material_blocks.material_section_id',
            $section->id
        )
        ->exists();

    if ($hasStudentProgress) {
        return redirect()
            ->route(
                'teacher.missions.show',
                $mission
            )
            ->with(
                'error',
                'Bagian materi tidak dapat dihapus karena sudah memiliki riwayat aktivitas peserta didik. Nonaktifkan bagian melalui menu Edit Bagian.'
            );
    }

    /*
     * Jawaban dan poin Latihan Mikro pada seluruh
     * blok di bagian ini juga harus dilindungi.
     */
    $hasMicroExerciseAnswers = DB::table(
        'micro_exercise_answers'
    )
        ->join(
            'micro_exercise_questions',
            'micro_exercise_questions.id',
            '=',
            'micro_exercise_answers.micro_exercise_question_id'
        )
        ->join(
            'material_blocks',
            'material_blocks.id',
            '=',
            'micro_exercise_questions.material_block_id'
        )
        ->where(
            'material_blocks.material_section_id',
            $section->id
        )
        ->exists();

    if ($hasMicroExerciseAnswers) {
        return redirect()
            ->route(
                'teacher.missions.show',
                $mission
            )
            ->with(
                'error',
                'Bagian materi tidak dapat dihapus karena Latihan Mikro di dalamnya sudah memiliki jawaban peserta didik. Nonaktifkan bagian melalui menu Edit Bagian.'
            );
    }

    /*
     * Jika belum memiliki riwayat siswa, bagian
     * beserta blok dan soal yang belum dijawab
     * dapat dihapus melalui aturan cascade database.
     */
    $section->delete();

    return redirect()
        ->route(
            'teacher.missions.show',
            $mission
        )
        ->with(
            'success',
            'Bagian materi berhasil dihapus.'
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
