<x-layouts::app :title="__('Dashboard Peserta Didik')">
    @php
        /*
         * Nilai awal untuk mencegah halaman error ketika data
         * belum dikirimkan oleh controller.
         */
        $completedMissionCount = $completedMissionCount ?? 0;
        $totalXp = $totalXp ?? 0;
        $currentLevel = $currentLevel ?? 'Starter';
        $badgeCount = $badgeCount ?? 0;

        $missionProgress = max(
            0,
            min(100, (int) ($missionProgress ?? 0))
        );

        $pretestCompleted = $pretestCompleted ?? false;
        $materialCompleted = $materialCompleted ?? false;
        $microPracticeCompleted =
    $microPracticeCompleted ?? false;

$quizCompleted = $quizCompleted ?? false;

$finalChallengeCompleted =
    $finalChallengeCompleted ?? false;

$posttestCompleted =
    $posttestCompleted ?? false;

        $readinessItems = [
            [
                'label' => 'Pretest',
                'description' => 'Asesmen awal kemampuan',
                'completed' => $pretestCompleted,
            ],
            [
                'label' => 'Materi Prakelas',
                'description' => 'Membaca materi dan checkpoint',
                'completed' => $materialCompleted,
            ],
            [
                'label' => 'Latihan Mikro',
                'description' => 'Latihan singkat setiap materi',
                'completed' => $microPracticeCompleted,
            ],
            [
                'label' => 'Kuis Materi',
                'description' => 'Evaluasi pemahaman awal',
                'completed' => $quizCompleted,
            ],
        ];
        $missionSteps = [
    [
        'label' => 'Pretest',
        'completed' => $pretestCompleted,
    ],
    [
        'label' => 'Materi Prakelas',
        'completed' => $materialCompleted,
    ],
    [
        'label' => 'Latihan Mikro',
        'completed' => $microPracticeCompleted,
    ],
    [
        'label' => 'Kuis Materi',
        'completed' => $quizCompleted,
    ],
    [
        'label' => 'Final Challenge',
        'completed' => $finalChallengeCompleted,
    ],
    [
        'label' => 'Posttest',
        'completed' => $posttestCompleted,
    ],
];

        $completedReadinessCount = array_sum(
            array_map(
                fn ($item) => $item['completed'] ? 1 : 0,
                $readinessItems
            )
        );

        $readinessPercentage = (int) round(
            ($completedReadinessCount / count($readinessItems)) * 100
        );

        $isReady = $completedReadinessCount === count($readinessItems);
    @endphp

    <div class="quest-page">

        {{-- Hero --}}
        <section class="quest-hero">
            <div class="relative z-10 flex flex-col gap-7 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-violet-500"></span>
                        DASHBOARD PESERTA DIDIK
                    </div>

                    <h1 class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white">
                        Selamat datang,
                        <span class="bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500 bg-clip-text text-transparent">
                            {{ auth()->user()->name }}!
                        </span>
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300">
                        Selesaikan misi pembelajaran, kumpulkan XP, raih badge,
                        dan tingkatkan kemampuan computational thinking bersama
                        Questify.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a
                            href="#misi-aktif"
                            class="quest-button-primary"
                        >
                            <svg
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m5 3 14 9-14 9V3Z" />
                            </svg>

                            Lihat Misi Aktif
                        </a>

                        <span class="quest-badge">
                            Level {{ $currentLevel }}
                        </span>
                    </div>
                </div>

                {{-- Dekorasi Hero --}}
                <div class="relative hidden h-44 w-60 shrink-0 lg:block">
                    <div class="absolute right-2 top-1 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-violet-500 to-blue-800 shadow-2xl shadow-violet-500/30"></div>

                    <div class="absolute bottom-1 left-3 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/40 bg-white/80 shadow-xl backdrop-blur dark:bg-neutral-900/80">
                        <svg
                            class="size-14 text-violet-600 dark:text-violet-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M8 21h8" />
                            <path d="M12 17v4" />
                            <path d="M7 4h10v4a5 5 0 0 1-10 0V4Z" />
                            <path d="M7 6H4v2a4 4 0 0 0 4 4" />
                            <path d="M17 6h3v2a4 4 0 0 1-4 4" />
                        </svg>
                    </div>

                    <div class="absolute left-0 top-0 flex size-12 items-center justify-center rounded-2xl bg-amber-400 text-white shadow-lg shadow-amber-400/30">
                        <svg
                            class="size-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m13 2-9 12h7l-1 8 9-12h-7l1-8Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Ringkasan Perkembangan --}}
        <section>
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-neutral-950 dark:text-white">
                        Perjalanan Belajarmu
                    </h2>

                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        Ringkasan perkembangan dan penghargaan yang telah kamu
                        peroleh.
                    </p>
                </div>

                <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500">
                    Diperbarui otomatis
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Misi Selesai --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950 dark:text-blue-300">
                                Progres
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Misi Selesai
                        </p>

                        <p class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white">
                            {{ $completedMissionCount }}
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            Misi pembelajaran dituntaskan
                        </p>
                    </div>
                </article>

                {{-- Total XP --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m13 2-9 12h7l-1 8 9-12h-7l1-8Z" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-950 dark:text-amber-300">
                                Reward
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Total XP
                        </p>

                        <p class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white">
                            {{ $totalXp }}
                            <span class="text-xl text-amber-500">XP</span>
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            XP yang telah dikumpulkan
                        </p>
                    </div>
                </article>

                {{-- Level --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-600 dark:bg-violet-950 dark:text-violet-300">
                                Tingkat
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Level Saat Ini
                        </p>

                        <p class="mt-1 text-3xl font-bold text-neutral-950 dark:text-white">
                            {{ $currentLevel }}
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            Tingkat perjalanan belajar
                        </p>
                    </div>
                </article>

                {{-- Badge --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="8" r="5" />
                                    <path d="m8.5 12-1.5 9 5-3 5 3-1.5-9" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                                Koleksi
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Badge
                        </p>

                        <p class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white">
                            {{ $badgeCount }}
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            Penghargaan yang diperoleh
                        </p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Misi dan Kesiapan --}}
        <section
            id="misi-aktif"
            class="grid scroll-mt-6 gap-5 xl:grid-cols-[1.35fr_0.65fr]"
        >

            {{-- Misi Aktif --}}
            <article class="quest-card overflow-hidden">
                <div class="border-b border-neutral-200 px-6 py-6 dark:border-neutral-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <span class="quest-eyebrow">
                                MISI AKTIF
                            </span>

                            <h2 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
    {{ $activeMission?->title ?? 'Belum Ada Misi Aktif' }}
</h2>

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-neutral-500 dark:text-neutral-400">
    {{ $activeMission?->description
        ?? 'Belum ada misi pembelajaran aktif yang tersedia.' }}
</p>
                        </div>

                        <span class="w-fit rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                            {{ $activeMission !== null
    ? 'Misi ' . $activeMission->sequence
    : 'Belum Tersedia' }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-900/60 dark:bg-blue-950/30">
                            <p class="text-xs font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                                Fokus Materi
                            </p>

                            <p class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">
                                Konsep dan fungsi perulangan
                            </p>
                        </div>

                        <div class="rounded-2xl border border-violet-100 bg-violet-50/70 p-4 dark:border-violet-900/60 dark:bg-violet-950/30">
                            <p class="text-xs font-bold uppercase tracking-wide text-violet-600 dark:text-violet-300">
                                Tantangan
                            </p>

                            <p class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">
                                Menganalisis alur program
                            </p>
                        </div>

                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                            <p class="text-xs font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">
                                Aktivitas
                            </p>

                            <p class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">
                                Materi, latihan mikro, dan kuis
                            </p>
                        </div>

                        <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4 dark:border-amber-900/60 dark:bg-amber-950/30">
                            <p class="text-xs font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300">
                                Reward
                            </p>

                            <p class="mt-2 text-sm font-semibold text-neutral-900 dark:text-white">
                                XP dan badge penyelesaian
                            </p>
                        </div>
                    </div>

                    <div class="mt-7">
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-neutral-600 dark:text-neutral-300">
                                Progres misi
                            </span>

                            <span class="font-bold text-blue-600 dark:text-blue-300">
                                {{ $missionProgress }}%
                            </span>
                        </div>

                        <div class="h-3 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500 transition-all duration-500"
                                style="width: {{ $missionProgress }}%"
                            ></div>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($missionSteps as $step)
        <div
            @class([
                'flex items-center gap-3 rounded-2xl border p-3',
                'border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950/40' => $step['completed'],
                'border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900' => ! $step['completed'],
            ])
        >
            <div
                @class([
                    'flex size-8 shrink-0 items-center justify-center rounded-xl',
                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-300' => $step['completed'],
                    'bg-neutral-200 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400' => ! $step['completed'],
                ])
            >
                @if ($step['completed'])
                    <svg
                        class="size-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                @else
                    <span class="size-2 rounded-full bg-current"></span>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-neutral-900 dark:text-white">
                    {{ $step['label'] }}
                </p>

                <p
                    @class([
                        'mt-0.5 text-xs font-medium',
                        'text-emerald-600 dark:text-emerald-400' => $step['completed'],
                        'text-neutral-500 dark:text-neutral-400' => ! $step['completed'],
                    ])
                >
                    {{ $step['completed'] ? 'Selesai' : 'Belum selesai' }}
                </p>
            </div>
        </div>
    @endforeach
</div>
                    </div>

                    <div
    class="mt-7 flex flex-col gap-4 border-t border-neutral-200 pt-6 sm:flex-row sm:items-center sm:justify-between"
>
    <div>
        <p class="text-sm font-bold text-neutral-900 dark:text-white">
            Status misi
        </p>

        @if ($activeMission === null)
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                Belum ada misi pembelajaran aktif yang tersedia.
            </p>
        @elseif ($missionProgress >= 100)
            <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                Selamat! Kamu telah menyelesaikan seluruh tahapan misi ini.
            </p>
        @elseif ($missionProgress > 0)
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                Lanjutkan tahapan pembelajaran yang belum selesai.
            </p>
        @else
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                Misi siap dimulai. Kerjakan setiap tahapan secara berurutan.
            </p>
        @endif
    </div>

    @if ($activeMission !== null)
        <a
            href="{{ route(
                'student.missions.show',
                ['mission' => $activeMission->slug]
            ) }}"
            class="quest-button-primary inline-flex items-center justify-center gap-2"
            wire:navigate
        >
            @if ($missionProgress >= 100)
                <span>✓</span>
                Misi Selesai
            @elseif ($missionProgress > 0)
                <span>▶</span>
                Lanjutkan Misi
            @else
                <span>▶</span>
                Mulai Misi
            @endif
        </a>
    @else
        <button
            type="button"
            disabled
            class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-xl bg-neutral-300 px-5 py-3 text-sm font-semibold text-neutral-500"
        >
            Belum Tersedia
        </button>
    @endif
</div>
                </div>
            </article>

            {{-- Kesiapan Belajar --}}
            <article
                id="kesiapan-belajar"
                class="quest-card p-6"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="quest-eyebrow">
                            PRAKELAS
                        </span>

                        <h2 class="mt-4 text-xl font-bold text-neutral-950 dark:text-white">
                            Kesiapan Belajar
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                            Selesaikan seluruh kegiatan sebelum pembelajaran
                            tatap muka.
                        </p>
                    </div>

                    <div class="flex size-16 shrink-0 items-center justify-center rounded-full border-6 border-blue-100 bg-white text-lg font-bold text-blue-600 dark:border-blue-950 dark:bg-neutral-900 dark:text-blue-300">
                        {{ $readinessPercentage }}%
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @foreach ($readinessItems as $item)
                        <div class="flex items-center gap-3 rounded-2xl border border-neutral-200 p-4 dark:border-neutral-800">
                            <div
                                @class([
                                    'flex size-9 shrink-0 items-center justify-center rounded-xl',
                                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' => $item['completed'],
                                    'bg-neutral-100 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500' => ! $item['completed'],
                                ])
                            >
                                @if ($item['completed'])
                                    <svg
                                        class="size-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="m5 12 4 4L19 6" />
                                    </svg>
                                @else
                                    <span class="size-2 rounded-full bg-current"></span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-neutral-900 dark:text-white">
                                    {{ $item['label'] }}
                                </p>

                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $item['description'] }}
                                </p>
                            </div>

                            <span
                                @class([
                                    'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' => $item['completed'],
                                    'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400' => ! $item['completed'],
                                ])
                            >
                                {{ $item['completed'] ? 'Selesai' : 'Belum' }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div
                    @class([
                        'mt-5 rounded-2xl border p-4',
                        'border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950/40' => $isReady,
                        'border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950/40' => ! $isReady,
                    ])
                >
                    <p
                        @class([
                            'text-sm font-bold',
                            'text-emerald-700 dark:text-emerald-300' => $isReady,
                            'text-amber-700 dark:text-amber-300' => ! $isReady,
                        ])
                    >
                        {{ $isReady
                            ? 'Kamu siap mengikuti pembelajaran!'
                            : 'Kegiatan prakelas belum lengkap' }}
                    </p>

                    <p class="mt-1 text-xs leading-5 text-neutral-600 dark:text-neutral-400">
                        {{ $completedReadinessCount }} dari
                        {{ count($readinessItems) }} aktivitas telah
                        diselesaikan.
                    </p>
                </div>
            </article>
        </section>

        {{-- Computational Thinking --}}
        <section class="quest-card overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-6 dark:border-neutral-800">
                <span class="quest-eyebrow">
                    COMPUTATIONAL THINKING
                </span>

                <h2 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
                    Kemampuan yang Akan Kamu Latih
                </h2>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                    Setiap misi Questify dirancang untuk melatih empat komponen
                    utama computational thinking secara terpadu.
                </p>
            </div>

            <div class="grid gap-4 p-6 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Decomposition --}}
                <article class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-5 dark:border-blue-900/60 dark:from-blue-950/40 dark:to-neutral-950">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-500/20">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300">
                        Dekomposisi
                    </p>

                    <h3 class="mt-2 text-lg font-bold text-neutral-950 dark:text-white">
                        Memecah Masalah
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Membagi masalah kompleks menjadi bagian-bagian yang
                        lebih kecil dan mudah diselesaikan.
                    </p>
                </article>

                {{-- Pattern Recognition --}}
                <article class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-5 dark:border-violet-900/60 dark:from-violet-950/40 dark:to-neutral-950">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-lg shadow-violet-500/20">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="6" cy="6" r="2" />
                            <circle cx="18" cy="6" r="2" />
                            <circle cx="6" cy="18" r="2" />
                            <circle cx="18" cy="18" r="2" />
                            <path d="M8 6h8M6 8v8M18 8v8M8 18h8" />
                        </svg>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-wide text-violet-600 dark:text-violet-300">
                        Pengenalan Pola
                    </p>

                    <h3 class="mt-2 text-lg font-bold text-neutral-950 dark:text-white">
                        Menemukan Kesamaan
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Mengenali pola, kesamaan, dan hubungan dari beberapa
                        permasalahan.
                    </p>
                </article>

                {{-- Abstraction --}}
                <article class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 dark:border-emerald-900/60 dark:from-emerald-950/40 dark:to-neutral-950">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-500/20">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="9" />
                            <path d="M8 12h8M12 8v8" />
                        </svg>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">
                        Abstraksi
                    </p>

                    <h3 class="mt-2 text-lg font-bold text-neutral-950 dark:text-white">
                        Memilih Informasi Penting
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Memusatkan perhatian pada informasi yang relevan dan
                        mengabaikan detail yang tidak diperlukan.
                    </p>
                </article>

                {{-- Algorithmic Thinking --}}
                <article class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 dark:border-amber-900/60 dark:from-amber-950/40 dark:to-neutral-950">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-amber-500 text-white shadow-lg shadow-amber-500/20">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M5 5h6v6H5zM13 13h6v6h-6z" />
                            <path d="M11 8h4a2 2 0 0 1 2 2v3" />
                            <path d="m14 10 3 3 3-3" />
                        </svg>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-wide text-amber-600 dark:text-amber-300">
                        Berpikir Algoritmik
                    </p>

                    <h3 class="mt-2 text-lg font-bold text-neutral-950 dark:text-white">
                        Menyusun Langkah Solusi
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Menentukan urutan langkah yang logis, sistematis, dan
                        dapat dijalankan.
                    </p>
                </article>
            </div>
        </section>

        {{-- Tips Belajar --}}
        <section class="grid gap-5 lg:grid-cols-3">
            <article class="quest-card p-5">
                <div class="flex items-start gap-4">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="font-bold text-neutral-950 dark:text-white">
                            Baca Secara Bertahap
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                            Selesaikan setiap bagian materi dan checkpoint
                            sebelum berpindah.
                        </p>
                    </div>
                </div>
            </article>

            <article class="quest-card p-5">
                <div class="flex items-start gap-4">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-300">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M9 12h6M12 9v6" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="font-bold text-neutral-950 dark:text-white">
                            Jangan Takut Mencoba
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                            Gunakan latihan mikro untuk mencoba dan memperbaiki
                            pemahaman.
                        </p>
                    </div>
                </div>
            </article>

            <article class="quest-card p-5">
                <div class="flex items-start gap-4">
                    <div class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="font-bold text-neutral-950 dark:text-white">
                            Fokus pada Proses
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                            XP dan badge adalah penghargaan atas proses belajar
                            yang kamu selesaikan.
                        </p>
                    </div>
                </div>
            </article>
        </section>

        {{-- Footer --}}
        <section class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400">
            <p>
                Questify — Learn by Doing. Think by Solving.
            </p>

            <span class="font-medium text-quest-600 dark:text-quest-300">
                Student Learning Journey
            </span>
        </section>
    </div>
</x-layouts::app>
