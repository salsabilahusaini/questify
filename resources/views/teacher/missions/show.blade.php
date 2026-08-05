<x-layouts::app :title="__('Detail Misi Pembelajaran')">
    @php
        $stages = $stages ?? collect();
        $materialBlocks = $materialBlocks ?? collect();

        $materialSectionCount =
            $materialSectionCount ?? 0;

        $materialBlockCount =
            $materialBlockCount ?? 0;

        $activeMaterialBlockCount =
            $activeMaterialBlockCount ?? 0;

        $requiredMaterialBlockCount =
            $requiredMaterialBlockCount ?? 0;

        $microExerciseQuestionCount =
            $microExerciseQuestionCount ?? 0;

        $configuredStageCount =
            $configuredStageCount ?? 0;

        $configuredStagePercentage =
            $configuredStagePercentage ?? 0;

        $formatDuration = function (
            int $seconds
        ): string {
            if ($seconds <= 0) {
                return '0 detik';
            }

            $minutes = intdiv($seconds, 60);
            $remainingSeconds = $seconds % 60;

            if ($minutes > 0) {
                return "{$minutes} menit "
                    . "{$remainingSeconds} detik";
            }

            return "{$remainingSeconds} detik";
        };
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

                            Detail Misi Pembelajaran
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $mission->title }}
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            {{ $mission->description
                                ?: 'Belum ada deskripsi misi.' }}
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.index'
                        ) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                    >
                        <span>←</span>
                        Kembali ke Daftar Misi
                    </a>
                </div>
            </div>
        </section>

        @if (session('success'))
    <section
        class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300"
    >
        ✓ {{ session('success') }}
    </section>
@endif

        {{-- Informasi utama misi --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-xl font-bold text-white shadow-sm"
                    >
                        {{ $mission->sequence }}
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                        >
                            Misi Pembelajaran
                        </p>

                        <h2
                            class="mt-1 text-xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $mission->title }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Slug: {{ $mission->slug }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
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
        'teacher.missions.edit',
        $mission
    ) }}"
    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"
>
    Edit Misi
    <span>✎</span>
</a>
                </div>
            </div>
        </section>

        {{-- Ringkasan --}}
        <section
            class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
        >
            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Bagian Materi
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $materialSectionCount }}
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Seluruh Blok
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $materialBlockCount }}
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Blok Wajib
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $requiredMaterialBlockCount }}
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Penyelesaian Misi
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $mission->completions_count }}
                </p>
            </article>
        </section>

        {{-- Kesiapan tahapan --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Kesiapan Konten
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Tahapan Pembelajaran
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ $configuredStageCount }}
                        dari 6 tahap telah dikonfigurasi.
                    </p>
                </div>

                <p
                    class="text-3xl font-bold text-violet-600"
                >
                    {{ $configuredStagePercentage }}%
                </p>
            </div>

            <div
                class="mt-5 h-3 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
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
                class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                @foreach ($stages as $stage)
                    <article
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

                            @if ($stage['configured'])
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-xs text-white"
                                >
                                    ✓
                                </span>
                            @else
                                <span
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-400 text-xs text-white"
                                >
                                    !
                                </span>
                            @endif
                        </div>

                        <p
                            class="mt-4 font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $stage['detail'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>
        <section class="quest-card p-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Pengelolaan Aktivitas
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950"
            >
                Soal Pretest
            </h2>

            <p class="mt-2 text-sm text-neutral-500">
                Kelola pertanyaan, pilihan jawaban, kunci
                jawaban, urutan, dan status soal Pretest.
            </p>
        </div>

        <a
            href="{{ route(
                'teacher.missions.pretest.index',
                $mission
            ) }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
        >
            Kelola Soal Pretest
            <span>→</span>
        </a>
    </div>
</section>
<section class="quest-card p-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <p
                class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
            >
                Pengelolaan Aktivitas
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950"
            >
                Latihan Mikro
            </h2>

            <p class="mt-2 text-sm text-neutral-500">
                Kelola soal penguatan pada setiap blok materi,
                pilihan jawaban, poin, dan umpan balik.
            </p>
        </div>

        <a
            href="{{ route(
                'teacher.missions.micro-exercises.index',
                $mission
            ) }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
        >
            Kelola Latihan Mikro
            <span>→</span>
        </a>
    </div>
</section>
<section class="quest-card p-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Pengelolaan Aktivitas
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950"
            >
                Kuis Materi
            </h2>

            <p class="mt-2 text-sm text-neutral-500">
                Kelola pertanyaan, pilihan jawaban, kunci
                jawaban, poin, dan penjelasan Kuis Materi.
            </p>
        </div>

        <a
            href="{{ route(
                'teacher.missions.quizzes.index',
                $mission
            ) }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-violet-700"
        >
            Kelola Kuis Materi
            <span>→</span>
        </a>
    </div>
</section>
<section class="quest-card p-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <p
                class="text-xs font-semibold uppercase tracking-wider text-amber-600"
            >
                Pengelolaan Aktivitas
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950"
            >
                Final Challenge
            </h2>

            <p class="mt-2 text-sm text-neutral-500">
                Kelola skenario, petunjuk, kode awal,
                output yang diharapkan, poin maksimal,
                dan status tantangan akhir.
            </p>
        </div>

        <a
            href="{{ route(
                'teacher.missions.final-challenge.index',
                $mission
            ) }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600"
        >
            Kelola Final Challenge
            <span>→</span>
        </a>
    </div>
</section>

<section class="quest-card p-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <p
                class="text-xs font-semibold uppercase tracking-wider text-rose-600"
            >
                Pengelolaan Aktivitas
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Soal Posttest
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Kelola pertanyaan, pilihan jawaban, kunci
                jawaban, urutan, dan status soal Posttest.
            </p>
        </div>

        <a
            href="{{ route(
                'teacher.missions.posttest.index',
                $mission
            ) }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700"
        >
            Kelola Soal Posttest
            <span>→</span>
        </a>
    </div>
</section>

        {{-- Detail materi --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Struktur Materi
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Bagian dan Blok Materi
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Rincian materi aktif, waktu baca minimum,
                kewajiban blok, dan jumlah Latihan Mikro.
            </p>
            <div class="mt-5 flex justify-end">
    <a
        href="{{ route(
            'teacher.missions.material-sections.create',
            $mission
        ) }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
    >
        Tambah Bagian Materi
        <span>+</span>
    </a>
</div>

            @if ($mission->materialSections->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-10 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada bagian materi
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach (
                        $mission->materialSections
                        as $section
                    )
                        <article
                            class="overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700"
                        >
                            <div
                                class="flex flex-col gap-3 border-b border-neutral-200 bg-neutral-50 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:bg-neutral-800/60"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                                    >
                                        Bagian
                                        {{ $section->sequence }}
                                    </p>

                                    <h3
                                        class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                                    >
                                        {{ $section->title }}
                                    </h3>

                                    @if ($section->description)
                                        <p
                                            class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                                        >
                                            {{ $section->description }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
    @if ($section->is_active)
        <span
            class="w-fit rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
        >
            Aktif
        </span>
    @else
        <span
            class="w-fit rounded-full bg-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300"
        >
            Nonaktif
        </span>
    @endif
<a
    href="{{ route(
        'teacher.missions.material-blocks.create',
        [
            'mission' => $mission,
            'section' => $section,
        ]
    ) }}"
    class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"
>
    Tambah Blok
    <span>+</span>
</a>
    <a
        href="{{ route(
            'teacher.missions.material-sections.edit',
            [
                'mission' => $mission,
                'section' => $section,
            ]
        ) }}"
        class="inline-flex items-center gap-1.5 rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700"
    >
        Edit Bagian
        <span>✎</span>
    </a>
</div>
                            </div>

                            @if ($section->blocks->isEmpty())
                                <div class="p-5">
                                    <p
                                        class="text-sm text-neutral-500 dark:text-neutral-400"
                                    >
                                        Belum ada blok materi.
                                    </p>
                                </div>
                            @else
                                <div
                                    class="grid gap-4 p-5 md:grid-cols-2"
                                >
                                    @foreach (
                                        $section->blocks
                                        as $block
                                    )
                                        @php
                                            $blockIcon = match (
                                                $block->type
                                            ) {
                                                'heading' => 'H',
                                                'paragraph' => '¶',
                                                'code' => '</>',
                                                'image' => '▧',
                                                'checkpoint' => '✓',
                                                default => '◆',
                                            };
                                        @endphp

                                        <div
                                            class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                                        >
                                            <div
                                                class="flex items-start justify-between gap-4"
                                            >
                                                <div
                                                    class="flex gap-3"
                                                >
                                                    <div
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-sm font-bold text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                                                    >
                                                        {{ $blockIcon }}
                                                    </div>

                                                    <div>
                                                        <p
                                                            class="text-xs font-semibold uppercase tracking-wide text-blue-600"
                                                        >
                                                            Blok
                                                            {{ $block->sequence }}
                                                        </p>

                                                        <h4
                                                            class="mt-1 font-bold text-neutral-950 dark:text-white"
                                                        >
                                                            {{ $block->title
                                                                ?: ucfirst(
                                                                    $block->type
                                                                ) }}
                                                        </h4>

                                                        <p
                                                            class="mt-1 text-xs text-neutral-500 dark:text-neutral-400"
                                                        >
                                                            Jenis:
                                                            {{ ucfirst(
                                                                $block->type
                                                            ) }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col items-end gap-2">
    @if ($block->is_active)
        <span
            class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
        >
            Aktif
        </span>
    @else
        <span
            class="rounded-full bg-neutral-200 px-2.5 py-1 text-[10px] font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300"
        >
            Nonaktif
        </span>
    @endif

    <a
        href="{{ route(
            'teacher.missions.material-blocks.edit',
            [
                'mission' => $mission,
                'block' => $block,
            ]
        ) }}"
        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-bold text-white shadow-sm transition hover:bg-blue-700"
    >
        Edit Blok
        <span>✎</span>
    </a>
</div>
                                            </div>

                                            <div
                                                class="mt-4 grid gap-3 sm:grid-cols-3"
                                            >
                                                <div>
                                                    <p
                                                        class="text-[10px] font-semibold uppercase tracking-wide text-neutral-400"
                                                    >
                                                        Waktu Minimum
                                                    </p>

                                                    <p
                                                        class="mt-1 text-sm font-bold text-neutral-900 dark:text-white"
                                                    >
                                                        {{ $formatDuration(
                                                            (int) $block
                                                                ->minimum_read_seconds
                                                        ) }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p
                                                        class="text-[10px] font-semibold uppercase tracking-wide text-neutral-400"
                                                    >
                                                        Kewajiban
                                                    </p>

                                                    <p
                                                        class="mt-1 text-sm font-bold text-neutral-900 dark:text-white"
                                                    >
                                                        {{ $block->is_required
                                                            ? 'Wajib'
                                                            : 'Opsional' }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <p
                                                        class="text-[10px] font-semibold uppercase tracking-wide text-neutral-400"
                                                    >
                                                        Latihan Mikro
                                                    </p>

                                                    <p
                                                        class="mt-1 text-sm font-bold text-violet-600"
                                                    >
                                                        {{ $block
                                                            ->micro_exercise_questions_count }}
                                                        soal
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
