<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\MaterialBlockProgress;
use App\Models\Mission;
use App\Models\PretestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialProgressController extends Controller
{
    /**
     * Mencatat saat blok materi mulai dibuka.
     */
    public function open(
        Request $request,
        Mission $mission,
        MaterialBlock $block
    ): JsonResponse {
        $this->ensureAccess($request, $mission, $block);

        $progress = MaterialBlockProgress::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'material_block_id' => $block->id,
            ],
            [
                'first_opened_at' => now(),
                'last_activity_at' => now(),
                'total_read_seconds' => 0,
                'visit_count' => 0,
                'is_completed' => false,
            ]
        );

        $progress->first_opened_at =
            $progress->first_opened_at ?? now();

        $progress->last_activity_at = now();
        $progress->visit_count = $progress->visit_count + 1;

        if (
            ! $progress->is_completed
            && $block->minimum_read_seconds === 0
        ) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        return response()->json([
            'message' => 'Blok materi berhasil dibuka.',
            'progress_id' => $progress->id,
            'total_read_seconds' => $progress->total_read_seconds,
            'minimum_read_seconds' => $block->minimum_read_seconds,
            'is_completed' => $progress->is_completed,
        ]);
    }

    /**
     * Menambahkan durasi baca yang dikirim secara berkala.
     */
    public function heartbeat(
        Request $request,
        Mission $mission,
        MaterialBlock $block
    ): JsonResponse {
        $this->ensureAccess($request, $mission, $block);

        $validated = $request->validate([
            'seconds' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],
        ]);

        $progress = MaterialBlockProgress::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'material_block_id' => $block->id,
            ],
            [
                'first_opened_at' => now(),
                'last_activity_at' => now(),
                'total_read_seconds' => 0,
                'visit_count' => 1,
                'is_completed' => false,
            ]
        );

        $progress->first_opened_at =
            $progress->first_opened_at ?? now();

        $progress->total_read_seconds =
            $progress->total_read_seconds + $validated['seconds'];

        $progress->last_activity_at = now();

        if (
            ! $progress->is_completed
            && $progress->total_read_seconds
                >= $block->minimum_read_seconds
        ) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        }

        $progress->save();

        return response()->json([
            'message' => 'Durasi baca berhasil diperbarui.',
            'total_read_seconds' => $progress->total_read_seconds,
            'minimum_read_seconds' => $block->minimum_read_seconds,
            'is_completed' => $progress->is_completed,
        ]);
    }

    /**
     * Memastikan siswa berhak mengakses blok materi.
     */
    private function ensureAccess(
        Request $request,
        Mission $mission,
        MaterialBlock $block
    ): void {
        abort_unless($mission->is_published, 404);

        $blockBelongsToMission = $block->is_active
            && $block->materialSection()
                ->where('mission_id', $mission->id)
                ->where('is_active', true)
                ->exists();

        abort_unless($blockBelongsToMission, 404);

        $pretestCompleted = $mission->pretestAttempts()
    ->where('user_id', $request->user()->id)
    ->where(
        'status',
        PretestAttempt::STATUS_COMPLETED
    )
    ->exists();

        abort_unless(
            $pretestCompleted,
            403,
            'Selesaikan Pretest terlebih dahulu.'
        );
    }
}
