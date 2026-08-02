<x-layouts::app :title="__('Monitoring Peserta Didik')">
    @php
        $studentMonitoringRows =
            $studentMonitoringRows ?? collect();

        $studentCount = $studentCount ?? 0;
        $readyStudentCount = $readyStudentCount ?? 0;
        $attentionStudentCount =
            $attentionStudentCount ?? 0;

        $focusedMission = $focusedMission ?? null;

        $requiredMaterialBlockCount =
            $requiredMaterialBlockCount ?? 0;

        $readinessPercentage = $studentCount > 0
            ? (int) round(
                ($readyStudentCount / $studentCount) * 100
            )
            : 0;
    @endphp

    <div class="quest-page">
        {{-- Header --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-violet-500"
                            ></span>

                            Monitoring Pembelajaran
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Monitoring Peserta Didik
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Pantau kesiapan prakelas, hasil aktivitas,
                            waktu membaca, XP, misi, dan pencapaian
                            peserta didik.
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

        {{-- Misi yang dipantau --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Misi Aktif
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
                        Status siap belajar ditentukan berdasarkan
                        penyelesaian Pretest, Materi Prakelas,
                        Latihan Mikro, dan Kuis Materi.
                    </p>
                </div>

                @if ($focusedMission)
                    <span
                        class="w-fit rounded-full bg-blue-100 px-4 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                    >
                        {{ $requiredMaterialBlockCount }}
                        blok materi wajib
                    </span>
                @endif
            </div>
        </section>

        {{-- Ringkasan --}}
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
                    Akun siswa yang terdaftar
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Siap Belajar
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $readyStudentCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Seluruh kegiatan prakelas selesai
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Perlu Perhatian
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $attentionStudentCount }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Aktivitas prakelas belum lengkap
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Kesiapan Kelas
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $readinessPercentage }}%
                </p>

                <div
                    class="mt-4 h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
                >
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500"
                        style="width:
                            {{ min(
                                100,
                                max(0, $readinessPercentage)
                            ) }}%;"
                    ></div>
                </div>
            </article>
        </section>

        {{-- Daftar siswa --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Rekap Aktivitas
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Daftar Peserta Didik
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Data diperbarui berdasarkan aktivitas terakhir
                        yang tercatat pada Questify.
                    </p>
                </div>

                <span
                    class="w-fit rounded-full bg-neutral-100 px-3 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                >
                    {{ $studentCount }} peserta didik
                </span>
            </div>

            @if ($studentMonitoringRows->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-10 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada peserta didik
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Data monitoring akan muncul setelah akun
                        peserta didik tersedia.
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($studentMonitoringRows as $row)
                        <article
                            class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900"
                        >
                            {{-- Identitas dan status --}}
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
                                            Aktivitas terakhir:
                                            @if ($row['last_activity_at'])
                                                {{ $row['last_activity_at']
                                                    ->locale('id')
                                                    ->translatedFormat(
                                                        'd F Y, H:i'
                                                    ) }}
                                            @else
                                                Belum ada aktivitas
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
    @if ($row['is_ready'])
        <span
            class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
        >
            <span
                class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-[10px] text-white"
            >
                ✓
            </span>

            Siap Belajar
        </span>
    @else
        <span
            class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
        >
            <span>!</span>
            Perlu Perhatian
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

                            {{-- Tahapan prakelas --}}
                            <div class="grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-4">
                                @php
                                    $stages = [
                                        [
                                            'label' => 'Pretest',
                                            'completed' =>
                                                $row['pretest_completed'],
                                            'value' =>
                                                $row['pretest_score'] !== null
                                                    ? number_format(
                                                        (float) $row['pretest_score'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) . '/100'
                                                    : '-',
                                            'theme' => 'blue',
                                        ],
                                        [
                                            'label' => 'Materi Prakelas',
                                            'completed' =>
                                                $row['material_completed'],
                                            'value' =>
                                                $row['completed_material_blocks']
                                                . ' dari '
                                                . $row['required_material_blocks']
                                                . ' blok',
                                            'theme' => 'violet',
                                        ],
                                        [
                                            'label' => 'Latihan Mikro',
                                            'completed' =>
                                                $row['micro_exercise_completed'],
                                            'value' =>
                                                $row['micro_exercise_score'] !== null
                                                    ? number_format(
                                                        (float) $row['micro_exercise_score'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) . '/100'
                                                    : '-',
                                            'theme' => 'emerald',
                                        ],
                                        [
                                            'label' => 'Kuis Materi',
                                            'completed' =>
                                                $row['quiz_completed'],
                                            'value' =>
                                                $row['quiz_score'] !== null
                                                    ? number_format(
                                                        (float) $row['quiz_score'],
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) . '/100'
                                                    : '-',
                                            'theme' => 'amber',
                                        ],
                                    ];
                                @endphp

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
                                                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                                >
                                                    Selesai
                                                </span>
                                            @else
                                                <span
                                                    class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                                >
                                                    Belum
                                                </span>
                                            @endif
                                        </div>

                                        <p
                                            class="mt-3 text-lg font-bold text-neutral-950 dark:text-white"
                                        >
                                            {{ $stage['value'] }}
                                        </p>

                                        @if (
                                            $stage['label']
                                            === 'Materi Prakelas'
                                        )
                                            <div
                                                class="mt-3 h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
                                            >
                                                <div
                                                    class="h-full rounded-full bg-gradient-to-r from-violet-500 to-blue-500"
                                                    style="width:
                                                        {{ min(
                                                            100,
                                                            max(
                                                                0,
                                                                $row[
                                                                    'material_progress_percentage'
                                                                ]
                                                            )
                                                        ) }}%;"
                                                ></div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Statistik aktivitas --}}
                            <div
                                class="grid gap-3 border-t border-neutral-200 bg-neutral-50 p-5 sm:grid-cols-2 lg:grid-cols-5 dark:border-neutral-700 dark:bg-neutral-900"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Waktu Membaca
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $row['formatted_read_time'] }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Kunjungan Materi
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $row['total_visits'] }}
                                        kali
                                    </p>
                                </div>

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

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Badge
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-violet-600"
                                    >
                                        {{ $row['earned_badges'] }}
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
