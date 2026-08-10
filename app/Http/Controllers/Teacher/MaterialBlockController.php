<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\MaterialSection;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialBlockController extends Controller
{
    /**
 * Menampilkan formulir tambah blok materi
 * pada bagian yang dipilih.
 */
public function create(
    Mission $mission,
    MaterialSection $section
): View {
    abort_unless(
        (int) $section->mission_id
            === (int) $mission->id,
        404
    );

    $nextSequence = (int) $section
        ->blocks()
        ->max('sequence') + 1;

    return view(
        'teacher.missions.material-blocks.create',
        [
            'mission' => $mission,
            'section' => $section,
            'nextSequence' => $nextSequence,
        ]
    );
}
/**
 * Menyimpan blok materi baru pada bagian
 * yang dipilih.
 */
public function store(
    Request $request,
    Mission $mission,
    MaterialSection $section
): RedirectResponse {
    abort_unless(
        (int) $section->mission_id
            === (int) $mission->id,
        404
    );

    $validated = $request->validate([
        'type' => [
            'required',
            Rule::in([
                MaterialBlock::TYPE_HEADING,
                MaterialBlock::TYPE_PARAGRAPH,
                MaterialBlock::TYPE_CODE,
                MaterialBlock::TYPE_IMAGE,
                MaterialBlock::TYPE_CHECKPOINT,
            ]),
        ],
        'title' => [
            'nullable',
            'string',
            'max:150',
        ],
        'content' => [
            'nullable',
            'string',
            'max:10000',
        ],
        'code_language' => [
            'nullable',
            'string',
            'max:50',
        ],
        'media_path' => [
            'nullable',
            'string',
            'max:2048',
        ],
        'sequence' => [
            'required',
            'integer',
            'min:1',
            Rule::unique(
                'material_blocks',
                'sequence'
            )->where(
                fn ($query) => $query->where(
                    'material_section_id',
                    $section->id
                )
            ),
        ],
        'minimum_read_seconds' => [
            'required',
            'integer',
            'min:0',
            'max:3600',
        ],
    ]);

    /*
     * Checkbox yang tidak dicentang tidak
     * dikirim oleh browser.
     */
    $validated['is_required'] =
        $request->boolean('is_required');

    $validated['is_active'] =
        $request->boolean('is_active');

        /*
 * Blok aktif dan wajib tidak boleh ditambahkan
 * setelah materi pada misi sudah memiliki
 * aktivitas peserta didik.
 */
if (
    $validated['is_required']
    && $validated['is_active']
) {
    $hasStudentMaterialActivity = DB::table(
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

    if ($hasStudentMaterialActivity) {
        return redirect()
            ->route(
                'teacher.missions.material-blocks.create',
                [
                    'mission' => $mission,
                    'section' => $section,
                ]
            )
            ->withInput()
            ->with(
                'error',
                'Blok aktif dan wajib tidak dapat ditambahkan karena materi sudah memiliki aktivitas peserta didik.'
            );
    }
}

    /*
     * Bahasa kode hanya digunakan pada
     * blok Kode Program.
     */
    if (
        $validated['type']
        !== MaterialBlock::TYPE_CODE
    ) {
        $validated['code_language'] = null;
    }

    /*
     * Path media hanya digunakan pada
     * blok Gambar.
     */
    if (
        $validated['type']
        !== MaterialBlock::TYPE_IMAGE
    ) {
        $validated['media_path'] = null;
    }

    $section->blocks()->create($validated);

    return redirect()
        ->route(
            'teacher.missions.show',
            $mission
        )
        ->with(
            'success',
            'Blok materi berhasil ditambahkan.'
        );
}
    /**
     * Menampilkan formulir edit blok materi.
     */
    public function edit(
        Mission $mission,
        MaterialBlock $block
    ): View {
        $this->ensureBlockBelongsToMission(
            $mission,
            $block
        );

        return view(
            'teacher.missions.material-blocks.edit',
            [
                'mission' => $mission,
                'block' => $block,
                'section' => $block->materialSection,
            ]
        );
    }

    /**
     * Menyimpan perubahan blok materi.
     */
    public function update(
        Request $request,
        Mission $mission,
        MaterialBlock $block
    ): RedirectResponse {
        $this->ensureBlockBelongsToMission(
            $mission,
            $block
        );

        $validated = $request->validate([
            'type' => [
                'required',
                Rule::in([
                    MaterialBlock::TYPE_HEADING,
                    MaterialBlock::TYPE_PARAGRAPH,
                    MaterialBlock::TYPE_CODE,
                    MaterialBlock::TYPE_IMAGE,
                    MaterialBlock::TYPE_CHECKPOINT,
                ]),
            ],
            'title' => [
                'nullable',
                'string',
                'max:150',
            ],
            'content' => [
                'nullable',
                'string',
                'max:10000',
            ],
            'code_language' => [
                'nullable',
                'string',
                'max:50',
            ],
            'media_path' => [
                'nullable',
                'string',
                'max:2048',
            ],
            'sequence' => [
    'required',
    'integer',
    'min:1',
    Rule::unique(
        'material_blocks',
        'sequence'
    )
        ->where(
            fn ($query) => $query->where(
                'material_section_id',
                $block->material_section_id
            )
        )
        ->ignore($block->id),
],
            'minimum_read_seconds' => [
                'required',
                'integer',
                'min:0',
                'max:3600',
            ],
        ]);

        /*
         * Checkbox yang tidak dicentang tidak dikirim
         * oleh browser.
         */
        $validated['is_required'] =
            $request->boolean('is_required');

        $validated['is_active'] =
            $request->boolean('is_active');

            /*
 * Blok yang sebelumnya belum aktif+wajib
 * tidak boleh dijadikan aktif+wajib setelah
 * materi memiliki aktivitas peserta didik.
 */
$becomesRequiredAndActive =
    $validated['is_required']
    && $validated['is_active']
    && (
        ! $block->is_required
        || ! $block->is_active
    );

if ($becomesRequiredAndActive) {
    $hasStudentMaterialActivity = DB::table(
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

    if ($hasStudentMaterialActivity) {
        return redirect()
            ->route(
                'teacher.missions.material-blocks.edit',
                [
                    'mission' => $mission,
                    'block' => $block,
                ]
            )
            ->withInput()
            ->with(
                'error',
                'Blok tidak dapat dijadikan aktif dan wajib karena materi sudah memiliki aktivitas peserta didik.'
            );
    }
}

        /*
         * Bahasa kode hanya digunakan untuk blok kode.
         */
        if (
            $validated['type']
            !== MaterialBlock::TYPE_CODE
        ) {
            $validated['code_language'] = null;
        }

        /*
         * Path media hanya digunakan untuk blok gambar.
         */
        if (
            $validated['type']
            !== MaterialBlock::TYPE_IMAGE
        ) {
            $validated['media_path'] = null;
        }
        /*
 * Blok yang sudah aktif dan wajib tidak boleh
 * diubah setelah Materi Prakelas pada misi
 * memiliki aktivitas peserta didik.
 */
$protectedBlockChanged =
    (bool) $block->is_required
    && (bool) $block->is_active
    && (
        $block->type !== $validated['type']
        || $block->title !== $validated['title']
        || $block->content !== $validated['content']
        || $block->code_language
            !== $validated['code_language']
        || $block->media_path
            !== $validated['media_path']
        || (int) $block->sequence
            !== (int) $validated['sequence']
        || (int) $block->minimum_read_seconds
            !== (int) $validated[
                'minimum_read_seconds'
            ]
        || (bool) $block->is_required
            !== (bool) $validated['is_required']
        || (bool) $block->is_active
            !== (bool) $validated['is_active']
    );

if ($protectedBlockChanged) {
    $hasStudentMaterialActivity = DB::table(
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

    if ($hasStudentMaterialActivity) {
        return redirect()
            ->route(
                'teacher.missions.material-blocks.edit',
                [
                    'mission' => $mission,
                    'block' => $block,
                ]
            )
            ->withInput()
            ->with(
                'error',
                'Blok aktif dan wajib tidak dapat diubah karena materi sudah memiliki aktivitas peserta didik.'
            );
    }
}

        $block->update($validated);

        return redirect()
            ->route(
                'teacher.missions.show',
                $mission
            )
            ->with(
                'success',
                'Blok materi berhasil diperbarui.'
            );
    }
    /**
 * Menghapus blok materi yang belum memiliki
 * riwayat aktivitas peserta didik.
 */
public function destroy(
    Mission $mission,
    MaterialBlock $block
): RedirectResponse {
    $this->ensureBlockBelongsToMission(
        $mission,
        $block
    );

    /*
     * Riwayat waktu baca, jumlah kunjungan,
     * dan penyelesaian blok harus dilindungi.
     */
    $hasStudentProgress = DB::table(
        'material_block_progresses'
    )
        ->where(
            'material_block_id',
            $block->id
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
                'Blok materi tidak dapat dihapus karena sudah memiliki riwayat aktivitas peserta didik. Nonaktifkan blok melalui menu Edit Blok.'
            );
    }

    /*
     * Jawaban dan poin Latihan Mikro yang berada
     * pada blok ini juga harus tetap terlindungi.
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
        ->where(
            'micro_exercise_questions.material_block_id',
            $block->id
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
                'Blok materi tidak dapat dihapus karena Latihan Mikro di dalamnya sudah memiliki jawaban peserta didik. Nonaktifkan blok melalui menu Edit Blok.'
            );
    }

    /*
     * Jika belum memiliki riwayat siswa, blok dapat
     * dihapus. Soal Latihan Mikro yang belum dijawab
     * ikut terhapus melalui aturan cascade database.
     */
    $block->delete();

    return redirect()
        ->route(
            'teacher.missions.show',
            $mission
        )
        ->with(
            'success',
            'Blok materi berhasil dihapus.'
        );
}

    /**
     * Memastikan blok benar-benar berasal dari misi
     * yang sedang dikelola.
     */
    private function ensureBlockBelongsToMission(
        Mission $mission,
        MaterialBlock $block
    ): void {
        $block->loadMissing(
            'materialSection:id,mission_id,title,sequence'
        );

        abort_unless(
            $block->materialSection !== null
                && (int) $block
                    ->materialSection
                    ->mission_id === (int) $mission->id,
            404
        );
    }
}
