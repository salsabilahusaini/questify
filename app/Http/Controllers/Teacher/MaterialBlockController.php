<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\Mission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaterialBlockController extends Controller
{
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
