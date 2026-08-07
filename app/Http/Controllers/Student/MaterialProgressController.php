<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaterialBlock;
use App\Models\MaterialBlockProgress;
use App\Models\Mission;
use App\Models\PretestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    && $block->type !== MaterialBlock::TYPE_CHECKPOINT
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

    $result = DB::transaction(function () use (
        $request,
        $block,
        $validated
    ): array {
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

        $progress = MaterialBlockProgress::query()
            ->whereKey($progress->id)
            ->lockForUpdate()
            ->firstOrFail();

        $now = now();

        $lastActivityAt = $progress->last_activity_at
            ?? $progress->first_opened_at
            ?? $now;

        $elapsedSeconds = max(
            0,
            $now->getTimestamp()
                - $lastActivityAt->getTimestamp()
        );

        $creditedSeconds = min(
            $validated['seconds'],
            $elapsedSeconds
        );

        if (
            ! $progress->is_completed
            && $creditedSeconds > 0
        ) {
            $progress->total_read_seconds +=
                $creditedSeconds;

            $progress->last_activity_at = $now;

            if (
    $block->type !== MaterialBlock::TYPE_CHECKPOINT
    && $progress->total_read_seconds
        >= $block->minimum_read_seconds
) {
                $progress->is_completed = true;
                $progress->completed_at = $now;
            }

            $progress->save();
        }

        if (
    ! $progress->is_completed
    && $block->type !== MaterialBlock::TYPE_CHECKPOINT
    && $block->minimum_read_seconds === 0
) {
            $progress->is_completed = true;
            $progress->completed_at = $now;
            $progress->last_activity_at = $now;
            $progress->save();
        }

        return [
            'progress' => $progress->fresh(),
            'credited_seconds' => $creditedSeconds,
        ];
    });

    /** @var MaterialBlockProgress $progress */
    $progress = $result['progress'];

    return response()->json([
        'message' => 'Durasi baca berhasil diperbarui.',
        'credited_seconds' =>
            $result['credited_seconds'],
        'total_read_seconds' =>
            $progress->total_read_seconds,
        'minimum_read_seconds' =>
            $block->minimum_read_seconds,
        'is_completed' => $progress->is_completed,
    ]);
}
/**
 * Menandai Checkpoint sebagai selesai
 * setelah siswa melakukan konfirmasi.
 */
public function completeCheckpoint(
    Request $request,
    Mission $mission,
    MaterialBlock $block
): JsonResponse {
    $this->ensureAccess(
        $request,
        $mission,
        $block
    );

    abort_unless(
        $block->type === MaterialBlock::TYPE_CHECKPOINT,
        404
    );

    $progress = MaterialBlockProgress::query()
        ->where(
            'user_id',
            $request->user()->id
        )
        ->where(
            'material_block_id',
            $block->id
        )
        ->first();

    if ($progress === null) {
        return response()->json(
            [
                'message' =>
                    'Buka Checkpoint terlebih dahulu.',
            ],
            422
        );
    }

    if (
        $progress->total_read_seconds
        < $block->minimum_read_seconds
    ) {
        return response()->json(
            [
                'message' =>
                    'Penuhi waktu baca minimum sebelum menyelesaikan Checkpoint.',
                'total_read_seconds' =>
                    $progress->total_read_seconds,
                'minimum_read_seconds' =>
                    $block->minimum_read_seconds,
                'is_completed' => false,
            ],
            422
        );
    }

    if (! $progress->is_completed) {
        $progress->update([
            'is_completed' => true,
            'completed_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    return response()->json([
        'message' =>
            'Checkpoint berhasil diselesaikan.',
        'total_read_seconds' =>
            $progress->total_read_seconds,
        'minimum_read_seconds' =>
            $block->minimum_read_seconds,
        'is_completed' => true,
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
