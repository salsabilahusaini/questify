<x-layouts::app :title="__('Hasil Pembelajaran')">
    @php
        $learningResultRows =
            $learningResultRows ?? collect();

        $studentCount = $studentCount ?? 0;

        $completeResultCount =
            $completeResultCount ?? 0;

        $incompleteResultCount =
            $incompleteResultCount ?? 0;

        $averagePretestScore =
            $averagePretestScore ?? 0;

        $averageMicroExerciseScore =
            $averageMicroExerciseScore ?? 0;

        $averageQuizScore =
            $averageQuizScore ?? 0;

        $averageFinalChallengeScore =
            $averageFinalChallengeScore ?? 0;

        $overallAverageScore =
            $overallAverageScore ?? 0;

        $focusedMission = $focusedMission ?? null;

        $completionPercentage = $studentCount > 0
            ? (int) round(
                (
                    $completeResultCount
                    / $studentCount
                ) * 100
            )
            : 0;
    @endphp

    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-emerald-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-emerald-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-emerald-500"
                            ></span>

                            Rekapitulasi Pembelajaran
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Hasil Pembelajaran Peserta Didik
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Rekap nilai Pretest, Latihan Mikro,
                            Kuis Materi, Final Challenge, XP,
                            serta penyelesaian misi peserta didik.
                        </p>
                    </div>

                    <a
                        href="{{ route('teacher.dashboard') }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                    >
                        <span>←</span>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </section>

        {{-- Misi yang direkap --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Misi yang Direkap
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        {{ $focusedMission?->title
                            ?? 'Belum ada misi dipublikasikan' }}
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Nilai yang ditampilkan merupakan hasil
                        pengerjaan terbaru setiap peserta didik.
                    </p>
                </div>

                @if ($focusedMission)
                    <span
                        class="w-fit rounded-full bg-blue-100 px-4 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                    >
                        Misi Aktif
                    </span>
                @endif
            </div>
        </section>

        {{-- Statistik utama --}}
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Peserta Didik
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $studentCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Siswa yang masuk dalam rekap
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Hasil Lengkap
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $completeResultCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Seluruh aktivitas telah memiliki nilai
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Belum Lengkap
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $incompleteResultCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Masih ada aktivitas tanpa hasil
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Rata-Rata Keseluruhan
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ number_format(
                        $overallAverageScore,
                        0,
                        ',',
                        '.'
                    ) }}

                    <span class="text-lg text-violet-600">
                        / 100
                    </span>
                </p>

                <div
                    class="mt-4 h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
                >
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-blue-500 to-violet-500"
                        style="width:
                            {{ min(
                                100,
                                max(
                                    0,
                                    $overallAverageScore
                                )
                            ) }}%;"
                    ></div>
                </div>
            </article>
        </section>

        {{-- Kelengkapan hasil --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                    >
                        Kelengkapan Data
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Penyelesaian Seluruh Hasil
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Persentase peserta didik yang telah memiliki
                        nilai pada keempat aktivitas.
                    </p>
                </div>

                <p
                    class="text-3xl font-bold text-emerald-600"
                >
                    {{ $completionPercentage }}%
                </p>
            </div>

            <div
                class="mt-5 h-3 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
            >
                <div
                    class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500"
                    style="width:
                        {{ min(
                            100,
                            max(
                                0,
                                $completionPercentage
                            )
                        ) }}%;"
                ></div>
            </div>

            <p
                class="mt-3 text-xs font-semibold text-neutral-500 dark:text-neutral-400"
            >
                {{ $completeResultCount }}
                dari
                {{ $studentCount }}
                peserta didik memiliki hasil lengkap.
            </p>
        </section>

        {{-- Rata-rata per aktivitas --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Analisis Nilai
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Rata-Rata Setiap Aktivitas
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Rata-rata dihitung berdasarkan nilai yang sudah
                tersedia pada setiap aktivitas.
            </p>

            <div
                class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4"
            >
                <article
                    class="rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-800 dark:bg-blue-950/40"
                >
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                    >
                        📝
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Pretest
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                    >
                        {{ number_format(
                            $averagePretestScore,
                            0,
                            ',',
                            '.'
                        ) }}

                        <span class="text-base text-blue-600">
                            / 100
                        </span>
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-800 dark:bg-emerald-950/40"
                >
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                    >
                        ⚡
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-emerald-600"
                    >
                        Latihan Mikro
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                    >
                        {{ number_format(
                            $averageMicroExerciseScore,
                            0,
                            ',',
                            '.'
                        ) }}

                        <span class="text-base text-emerald-600">
                            / 100
                        </span>
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-violet-200 bg-violet-50 p-5 dark:border-violet-800 dark:bg-violet-950/40"
                >
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                    >
                        ❓
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Kuis Materi
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                    >
                        {{ number_format(
                            $averageQuizScore,
                            0,
                            ',',
                            '.'
                        ) }}

                        <span class="text-base text-violet-600">
                            / 100
                        </span>
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950/40"
                >
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                    >
                        🏆
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-amber-600"
                    >
                        Final Challenge
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                    >
                        {{ number_format(
                            $averageFinalChallengeScore,
                            0,
                            ',',
                            '.'
                        ) }}

                        <span class="text-base text-amber-600">
                            / 100
                        </span>
                    </p>
                </article>
            </div>
        </section>

        {{-- Daftar hasil siswa --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Rekap Individu
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Hasil Setiap Peserta Didik
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Nilai, rata-rata, XP, serta status
                        kelengkapan setiap peserta didik.
                    </p>
                </div>

                <span
                    class="w-fit rounded-full bg-neutral-100 px-3 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                >
                    {{ $studentCount }} peserta didik
                </span>
            </div>

            @if ($learningResultRows->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-10 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada hasil pembelajaran
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Rekap akan muncul setelah peserta didik
                        mengerjakan aktivitas.
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($learningResultRows as $row)
                        <article
                            class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900"
                        >
                            <div
                                class="flex flex-col gap-5 border-b border-neutral-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-sm"
                                    >
                                        {{ strtoupper(
                                            mb_substr(
                                                $row['student']->name
                                                    ?? 'SQ',
                                                0,
                                                2
                                            )
                                        ) }}
                                    </div>

                                    <div>
                                        <h3
                                            class="text-lg font-bold text-neutral-950 dark:text-white"
                                        >
                                            {{ $row['student']->name }}
                                        </h3>

                                        <p
                                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                        >
                                            {{ $row['student']->email }}
                                        </p>

                                        <p
                                            class="mt-2 text-xs text-neutral-400 dark:text-neutral-500"
                                        >
                                            {{ $row['completed_activity_count'] }}
                                            dari 4 aktivitas memiliki nilai
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-3"
                                >
                                    @if ($row['is_complete'])
                                        <span
                                            class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                        >
                                            <span>✓</span>
                                            Hasil Lengkap
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                        >
                                            <span>!</span>
                                            Belum Lengkap
                                        </span>
                                    @endif

                                    <a
                                        href="{{ route(
                                            'teacher.monitoring.show',
                                            $row['student']
                                        ) }}"
                                        class="inline-flex w-fit items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700"
                                    >
                                        Lihat Detail
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>

                            <div
                                class="grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-5"
                            >
                                @php
                                    $scores = [
                                        [
                                            'label' => 'Pretest',
                                            'score' =>
                                                $row['pretest_score'],
                                        ],
                                        [
                                            'label' => 'Latihan Mikro',
                                            'score' =>
                                                $row[
                                                    'micro_exercise_score'
                                                ],
                                        ],
                                        [
                                            'label' => 'Kuis Materi',
                                            'score' =>
                                                $row['quiz_score'],
                                        ],
                                        [
                                            'label' => 'Final Challenge',
                                            'score' =>
                                                $row[
                                                    'final_challenge_score'
                                                ],
                                        ],
                                    ];
                                @endphp

                                @foreach ($scores as $scoreItem)
                                    <div
                                        class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800/60"
                                    >
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400"
                                        >
                                            {{ $scoreItem['label'] }}
                                        </p>

                                        @if (
                                            $scoreItem['score']
                                            !== null
                                        )
                                            <p
                                                class="mt-3 text-xl font-bold text-neutral-950 dark:text-white"
                                            >
                                                {{ number_format(
                                                    $scoreItem['score'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                                <span
                                                    class="text-sm text-blue-600"
                                                >
                                                    / 100
                                                </span>
                                            </p>
                                        @else
                                            <p
                                                class="mt-3 text-sm font-semibold text-amber-600"
                                            >
                                                Belum tersedia
                                            </p>
                                        @endif
                                    </div>
                                @endforeach

                                <div
                                    class="rounded-2xl border border-violet-200 bg-violet-50 p-4 dark:border-violet-800 dark:bg-violet-950/40"
                                >
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-violet-600"
                                    >
                                        Rata-Rata
                                    </p>

                                    @if (
                                        $row['average_score']
                                        !== null
                                    )
                                        <p
                                            class="mt-3 text-xl font-bold text-neutral-950 dark:text-white"
                                        >
                                            {{ number_format(
                                                $row['average_score'],
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                            <span
                                                class="text-sm text-violet-600"
                                            >
                                                / 100
                                            </span>
                                        </p>
                                    @else
                                        <p
                                            class="mt-3 text-sm font-semibold text-amber-600"
                                        >
                                            Belum tersedia
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="grid gap-3 border-t border-neutral-200 bg-neutral-50 p-5 sm:grid-cols-2 dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Total XP
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-amber-600"
                                    >
                                        {{ number_format(
                                            $row['total_xp'],
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                        XP
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Misi Selesai
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-blue-600"
                                    >
                                        {{ $row['completed_missions'] }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
