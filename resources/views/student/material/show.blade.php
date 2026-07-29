<x-layouts::app :title="'Materi Prakelas - ' . $mission->title">
    <div class="quest-page">
        <section class="quest-hero">
            <div class="relative z-10">
                <div class="quest-eyebrow">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Materi Prakelas Misi {{ $mission->sequence }}
                </div>

                <h1 class="mt-5 text-3xl font-bold text-neutral-950 dark:text-white">
                    {{ $mission->title }}
                </h1>

                <p class="mt-3 max-w-3xl text-neutral-600 dark:text-neutral-300">
                    Pelajari materi secara bertahap sebelum mengikuti kegiatan
                    pembelajaran di kelas.
                </p>

                <div class="mt-6">
                    <a
                        href="{{ route('student.missions.show', ['mission' => $mission->slug]) }}"
                        class="quest-button-primary"
                        wire:navigate
                    >
                        Kembali ke Detail Misi
                    </a>
                </div>
            </div>
        </section>

        <section class="space-y-5">
    @foreach ($sections as $section)
        <article class="quest-card p-6">
            <div class="flex items-start gap-4">
                <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 font-bold text-white">
                    {{ $section->sequence }}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                        Bagian {{ $section->sequence }}
                    </p>

                    <h2 class="mt-2 text-xl font-bold text-neutral-950 dark:text-white">
                        {{ $section->title }}
                    </h2>

                    @if ($section->description)
                        <p class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300">
                            {{ $section->description }}
                        </p>
                    @endif

                    <p class="mt-4 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                        {{ $section->blocks->count() }} blok pembelajaran
                    </p>
                    <div class="mt-6 space-y-4 border-t border-neutral-200 pt-6 dark:border-neutral-700">
    @foreach ($section->blocks as $block)
    <div
        class="material-block"
        data-block-id="{{ $block->id }}"
        data-open-url="{{ route(
            'student.missions.material.blocks.open',
            [
                'mission' => $mission->slug,
                'block' => $block->id,
            ]
        ) }}"
        data-heartbeat-url="{{ route(
            'student.missions.material.blocks.heartbeat',
            [
                'mission' => $mission->slug,
                'block' => $block->id,
            ]
        ) }}"
        data-minimum-read-seconds="{{ $block->minimum_read_seconds }}"
    >
    @php
    $progress = $block->progresses->first();

    $totalReadSeconds = (int) (
        $progress?->total_read_seconds ?? 0
    );

    $minimumReadSeconds = (int) (
        $block->minimum_read_seconds ?? 0
    );

    $isCompleted = (bool) (
        $progress?->is_completed ?? false
    );

    $hasStarted = $progress !== null;

    $statusLabel = $isCompleted
        ? 'Selesai'
        : ($hasStarted ? 'Sedang Dibaca' : 'Belum Dibaca');

    $progressPercentage = $minimumReadSeconds > 0
        ? min(
            100,
            (int) round(
                ($totalReadSeconds / $minimumReadSeconds) * 100
            )
        )
        : 100;
@endphp

<div class="mb-3 flex flex-wrap items-center justify-between gap-3">
    <span
        @class([
    'material-status-label inline-flex rounded-full px-3 py-1 text-xs font-semibold',
            'bg-emerald-100 text-emerald-700' => $isCompleted,
            'bg-amber-100 text-amber-700' => ! $isCompleted && $hasStarted,
            'bg-neutral-100 text-neutral-600' => ! $hasStarted,
        ])
    >
        {{ $statusLabel }}
    </span>

    @if ($minimumReadSeconds > 0)
        <span class="material-time-label text-xs font-medium text-neutral-500">
            {{ $totalReadSeconds }} dari
            {{ $minimumReadSeconds }} detik
        </span>
    @endif
</div>

@if ($minimumReadSeconds > 0)
    <div class="mb-4 h-2 overflow-hidden rounded-full bg-neutral-100">
        <div
    class="material-progress-bar h-full rounded-full bg-blue-600 transition-all"
    style="width: {{ $progressPercentage }}%"
></div>
    </div>
@endif
        @switch($block->type)
            @case('heading')
                <div>
                    <h3 class="text-lg font-bold text-neutral-950 dark:text-white">
                        {{ $block->title }}
                    </h3>
                </div>
                @break

            @case('paragraph')
                <div class="rounded-2xl bg-neutral-50 p-5 dark:bg-neutral-800">
                    @if ($block->title)
                        <h3 class="mb-2 font-bold text-neutral-950 dark:text-white">
                            {{ $block->title }}
                        </h3>
                    @endif

                    <p class="text-sm leading-7 text-neutral-700 dark:text-neutral-300">
                        {{ $block->content }}
                    </p>
                </div>
                @break

            @case('code')
                <div class="overflow-hidden rounded-2xl bg-neutral-950">
                    @if ($block->title)
                        <div class="border-b border-neutral-700 px-5 py-3">
                            <p class="text-sm font-semibold text-white">
                                {{ $block->title }}
                            </p>
                        </div>
                    @endif

                    <pre class="overflow-x-auto p-5 text-sm leading-7 text-neutral-100"><code>{{ $block->content }}</code></pre>
                </div>
                @break

            @case('image')
                @if ($block->media_path)
                    <div class="overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700">
                        <img
                            src="{{ asset('storage/' . $block->media_path) }}"
                            alt="{{ $block->title ?? 'Gambar materi' }}"
                            class="h-auto w-full"
                        >
                    </div>
                @endif
                @break

            @case('checkpoint')
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-800 dark:bg-emerald-950">
                    @if ($block->title)
                        <h3 class="font-bold text-emerald-800 dark:text-emerald-300">
                            {{ $block->title }}
                        </h3>
                    @endif

                    @if ($block->content)
                        <p class="mt-2 text-sm leading-6 text-emerald-700 dark:text-emerald-400">
                            {{ $block->content }}
                        </p>
                    @endif
                </div>
                @break
        @endswitch
         </div>
    @endforeach
</div>
                </div>
            </div>
        </article>
    @endforeach
</section>
    </div>
    <script>
    function initializeMaterialTracking() {
        if (window.questifyMaterialTrackingCleanup) {
            window.questifyMaterialTrackingCleanup();
        }

        const materialBlocks = Array.from(
            document.querySelectorAll('.material-block')
        );

        if (materialBlocks.length === 0) {
            return;
        }

        const csrfToken = '{{ csrf_token() }}';
        const openedBlockIds = new Set();
        const blockVisibility = new Map();

        let activeBlock = null;
        let heartbeatInterval = null;
        let isSendingHeartbeat = false;

function updateBlockDisplay(block, result) {
    const statusLabel = block.querySelector(
        '.material-status-label'
    );

    const timeLabel = block.querySelector(
        '.material-time-label'
    );

    const progressBar = block.querySelector(
        '.material-progress-bar'
    );

    const totalReadSeconds = Number(
        result.total_read_seconds ?? 0
    );

    const minimumReadSeconds = Number(
        result.minimum_read_seconds
        ?? block.dataset.minimumReadSeconds
        ?? 0
    );

    const isCompleted = Boolean(result.is_completed);

    if (statusLabel) {
        statusLabel.textContent = isCompleted
            ? 'Selesai'
            : 'Sedang Dibaca';

        statusLabel.classList.remove(
            'bg-neutral-100',
            'text-neutral-600',
            'bg-amber-100',
            'text-amber-700',
            'bg-emerald-100',
            'text-emerald-700'
        );

        if (isCompleted) {
            statusLabel.classList.add(
                'bg-emerald-100',
                'text-emerald-700'
            );
        } else {
            statusLabel.classList.add(
                'bg-amber-100',
                'text-amber-700'
            );
        }
    }

    if (timeLabel && minimumReadSeconds > 0) {
        timeLabel.textContent =
            `${totalReadSeconds} dari `
            + `${minimumReadSeconds} detik`;
    }

    if (progressBar) {
        const percentage = minimumReadSeconds > 0
            ? Math.min(
                100,
                Math.round(
                    (
                        totalReadSeconds
                        / minimumReadSeconds
                    ) * 100
                )
            )
            : 100;

        progressBar.style.width = `${percentage}%`;
    }
}

        async function sendRequest(url, payload = {}) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error(
                    `Permintaan gagal dengan status ${response.status}`
                );
            }

            return response.json();
        }

        async function recordBlockOpen(block) {
            const blockId = block.dataset.blockId;

            if (openedBlockIds.has(blockId)) {
                return;
            }

            openedBlockIds.add(blockId);

            try {
                const result = await sendRequest(
                    block.dataset.openUrl
                );

                block.dataset.totalReadSeconds =
                    result.total_read_seconds;

                block.dataset.isCompleted =
                    result.is_completed ? 'true' : 'false';
                    updateBlockDisplay(block, result);
                    determineActiveBlock();
            } catch (error) {
                openedBlockIds.delete(blockId);

                console.error(
                    'Gagal mencatat pembukaan blok materi:',
                    error
                );
            }
        }

        function determineActiveBlock() {
    const visibleBlocks = materialBlocks
        .filter((block) => {
            const visibilityRatio =
                blockVisibility.get(block) ?? 0;

            const minimumReadSeconds = Number(
                block.dataset.minimumReadSeconds ?? 0
            );

            const isCompleted =
                block.dataset.isCompleted === 'true';

            return (
                visibilityRatio >= 0.35
                && minimumReadSeconds > 0
                && !isCompleted
            );
        })
        .sort((firstBlock, secondBlock) => {
            return (
                (blockVisibility.get(secondBlock) ?? 0)
                - (blockVisibility.get(firstBlock) ?? 0)
            );
        });

    activeBlock = visibleBlocks[0] ?? null;
}

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    const visibilityRatio = entry.isIntersecting
                        ? entry.intersectionRatio
                        : 0;

                    blockVisibility.set(
                        entry.target,
                        visibilityRatio
                    );

                    if (
                        entry.isIntersecting
                        && visibilityRatio >= 0.35
                    ) {
                        recordBlockOpen(entry.target);
                    }
                });

                determineActiveBlock();
            },
            {
                threshold: [0, 0.35, 0.5, 0.75, 1],
            }
        );

        materialBlocks.forEach((block) => {
            observer.observe(block);
        });

        heartbeatInterval = window.setInterval(
            async () => {
                if (
    !activeBlock
    || document.hidden
    || isSendingHeartbeat
    || activeBlock.dataset.isCompleted === 'true'
) {
    determineActiveBlock();
    return;
}

                isSendingHeartbeat = true;

                try {
                    const result = await sendRequest(
                        activeBlock.dataset.heartbeatUrl,
                        {
                            seconds: 5,
                        }
                    );

                    activeBlock.dataset.totalReadSeconds =
                        result.total_read_seconds;

                    activeBlock.dataset.isCompleted =
                        result.is_completed
                            ? 'true'
                            : 'false';
                            updateBlockDisplay(activeBlock, result);
                            if (result.is_completed) {
    determineActiveBlock();
}
                } catch (error) {
                    console.error(
                        'Gagal mencatat durasi baca:',
                        error
                    );
                } finally {
                    isSendingHeartbeat = false;
                }
            },
            5000
        );

        window.questifyMaterialTrackingCleanup = function () {
            observer.disconnect();

            if (heartbeatInterval) {
                window.clearInterval(heartbeatInterval);
            }

            activeBlock = null;
        };
    }

    document.addEventListener(
        'DOMContentLoaded',
        initializeMaterialTracking
    );

    document.addEventListener(
        'livewire:navigated',
        initializeMaterialTracking
    );

    document.addEventListener(
        'livewire:navigating',
        function () {
            if (window.questifyMaterialTrackingCleanup) {
                window.questifyMaterialTrackingCleanup();
            }
        }
    );
</script>
</x-layouts::app>
