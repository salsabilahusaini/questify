<x-layouts::app :title="__('Misi Pembelajaran Guru')">
    @php
        $missionRows = $missionRows ?? collect();

        $totalMissionCount =
            $totalMissionCount ?? 0;

        $publishedMissionCount =
            $publishedMissionCount ?? 0;

        $draftMissionCount =
            $draftMissionCount ?? 0;

        $totalCompletionCount =
            $totalCompletionCount ?? 0;
    @endphp

    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-blue-500"
                            ></span>

                            Pengelolaan Pembelajaran
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Misi Pembelajaran
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Lihat kesiapan konten, status publikasi,
                            XP, jumlah aktivitas, dan penyelesaian
                            setiap misi pembelajaran Questify.
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

        {{-- Ringkasan misi --}}
        <section
            class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
        >
            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Total Misi
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $totalMissionCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Seluruh misi yang tersedia
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Dipublikasikan
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $publishedMissionCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Dapat diakses peserta didik
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Draft
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $draftMissionCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Belum dipublikasikan
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Total Penyelesaian
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $totalCompletionCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Catatan misi yang telah diselesaikan
                </p>
            </article>
        </section>

        {{-- Daftar misi --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Daftar Konten
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Misi yang Tersedia
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Setiap misi terdiri atas Pretest, Materi
                        Prakelas, Latihan Mikro, Kuis Materi,
                        dan Final Challenge.
                    </p>
                </div>

                <span
                    class="w-fit rounded-full bg-neutral-100 px-3 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                >
                    {{ $totalMissionCount }} misi
                </span>
            </div>

            @if ($missionRows->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-10 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada misi pembelajaran
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Daftar misi akan muncul setelah data misi
                        tersedia.
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($missionRows as $row)
                        @php
                            $mission = $row['mission'];

                            $configuredStagePercentage =
                                (int) round(
                                    (
                                        $row[
                                            'configured_stage_count'
                                        ]
                                        / 5
                                    ) * 100
                                );

                            $stages = [
                                [
                                    'label' => 'Pretest',
                                    'completed' =>
                                        $row[
                                            'pretest_question_count'
                                        ] > 0,
                                    'detail' =>
                                        $row[
                                            'pretest_question_count'
                                        ] . ' soal',
                                ],
                                [
                                    'label' => 'Materi Prakelas',
                                    'completed' =>
                                        $row[
                                            'active_material_block_count'
                                        ] > 0,
                                    'detail' =>
                                        $row[
                                            'active_material_block_count'
                                        ] . ' blok aktif',
                                ],
                                [
                                    'label' => 'Latihan Mikro',
                                    'completed' =>
                                        $row[
                                            'micro_exercise_question_count'
                                        ] > 0,
                                    'detail' =>
                                        $row[
                                            'micro_exercise_question_count'
                                        ] . ' soal',
                                ],
                                [
                                    'label' => 'Kuis Materi',
                                    'completed' =>
                                        $row[
                                            'quiz_question_count'
                                        ] > 0,
                                    'detail' =>
                                        $row[
                                            'quiz_question_count'
                                        ] . ' soal',
                                ],
                                [
                                    'label' => 'Final Challenge',
                                    'completed' =>
                                        $row[
                                            'has_final_challenge'
                                        ],
                                    'detail' =>
                                        $row[
                                            'has_final_challenge'
                                        ]
                                            ? 'Tersedia'
                                            : 'Belum tersedia',
                                ],
                            ];
                        @endphp

                        <article
                            class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900"
                        >
                            {{-- Informasi utama --}}
                            <div
                                class="flex flex-col gap-5 border-b border-neutral-200 p-5 lg:flex-row lg:items-start lg:justify-between dark:border-neutral-700"
                            >
                                <div class="flex gap-4">
                                    <div
                                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-sm"
                                    >
                                        {{ $mission->sequence }}
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                                        >
                                            Misi Pembelajaran
                                        </p>

                                        <h3
                                            class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                                        >
                                            {{ $mission->title }}
                                        </h3>

                                        <p
                                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                                        >
                                            {{ $mission->description
                                                ?: 'Belum ada deskripsi misi.' }}
                                        </p>

                                        <p
                                            class="mt-2 text-xs text-neutral-400 dark:text-neutral-500"
                                        >
                                            Slug:
                                            {{ $mission->slug }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-3"
                                >
                                    @if ($mission->is_published)
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                        >
                                            <span
                                                class="h-2 w-2 rounded-full bg-emerald-500"
                                            ></span>

                                            Dipublikasikan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                        >
                                            <span
                                                class="h-2 w-2 rounded-full bg-amber-500"
                                            ></span>

                                            Draft
                                        </span>
                                    @endif

                                    <span
                                        class="rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                    >
                                        +{{ number_format(
                                            $mission->xp_reward,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                        XP
                                    </span>
                                    <a
    href="{{ route(
        'teacher.missions.show',
        $mission
    ) }}"
    class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700"
>
    Lihat Detail
    <span>→</span>
</a>
                                </div>
                            </div>

                            {{-- Kesiapan tahapan --}}
                            <div class="p-5">
                                <div
                                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                                        >
                                            Kesiapan Konten
                                        </p>

                                        <p
                                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                        >
                                            {{ $row[
                                                'configured_stage_count'
                                            ] }}
                                            dari 5 tahap telah dikonfigurasi.
                                        </p>
                                    </div>

                                    <p
                                        class="text-xl font-bold text-violet-600"
                                    >
                                        {{ $configuredStagePercentage }}%
                                    </p>
                                </div>

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
                                                    $configuredStagePercentage
                                                )
                                            ) }}%;"
                                    ></div>
                                </div>

                                <div
                                    class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-5"
                                >
                                    @foreach ($stages as $stage)
                                        <div
                                            class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800/60"
                                        >
                                            <div
                                                class="flex items-center justify-between gap-3"
                                            >
                                                <p
                                                    class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400"
                                                >
                                                    {{ $stage['label'] }}
                                                </p>

                                                @if ($stage['completed'])
                                                    <span
                                                        class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[10px] text-white"
                                                    >
                                                        ✓
                                                    </span>
                                                @else
                                                    <span
                                                        class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-400 text-[10px] text-white"
                                                    >
                                                        !
                                                    </span>
                                                @endif
                                            </div>

                                            <p
                                                class="mt-3 text-sm font-bold text-neutral-900 dark:text-white"
                                            >
                                                {{ $stage['detail'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Statistik misi --}}
                            <div
                                class="grid gap-3 border-t border-neutral-200 bg-neutral-50 p-5 sm:grid-cols-2 lg:grid-cols-5 dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Bagian Materi
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $row[
                                            'material_section_count'
                                        ] }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Seluruh Blok
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $row[
                                            'material_block_count'
                                        ] }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Blok Wajib
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-violet-600"
                                    >
                                        {{ $row[
                                            'required_material_block_count'
                                        ] }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Penyelesaian
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-emerald-600"
                                    >
                                        {{ $row[
                                            'completion_count'
                                        ] }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Diperbarui
                                    </p>

                                    <p
                                        class="mt-2 text-sm font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $mission->updated_at
                                            ->locale('id')
                                            ->translatedFormat(
                                                'd F Y, H:i'
                                            ) }}
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
