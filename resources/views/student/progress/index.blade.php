<x-layouts::app :title="'Progress dan XP'">
    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300"
                >
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    Perkembangan Belajar
                </span>

                <h1
                    class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                >
                    Progress dan XP
                </h1>

                <p
                    class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                >
                    Pantau penyelesaian misi, jumlah XP yang telah
                    diperoleh, dan perkembangan level pembelajaranmu.
                </p>
            </div>
        </section>

        {{-- Ringkasan progres --}}
        <section class="grid gap-4 md:grid-cols-3">
            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Total XP
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ number_format($totalXp, 0, ',', '.') }}
                    <span class="text-lg text-blue-600">XP</span>
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Diperoleh dari misi yang telah diselesaikan.
                </p>
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Level Saat Ini
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $level }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    @if ($nextLevelXp === null)
                        Kamu telah mencapai level tertinggi.
                    @else
                        {{ number_format($xpToNextLevel, 0, ',', '.') }}
                        XP lagi menuju level berikutnya.
                    @endif
                </p>
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Misi Selesai
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $completedMissions }}
                    <span class="text-lg text-emerald-600">
                        dari {{ $totalPublishedMissions }}
                    </span>
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    {{ number_format(
                        $missionProgressPercentage,
                        2,
                        ',',
                        '.'
                    ) }}% misi telah dituntaskan.
                </p>
            </div>
        </section>

        {{-- Perkembangan level --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Perkembangan Level
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Level {{ $level }}
                    </h2>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                    >
                        @if ($nextLevelXp === null)
                            Selamat, kamu telah mencapai level Advanced.
                        @else
                            Kumpulkan XP dengan menyelesaikan misi
                            pembelajaran berikutnya.
                        @endif
                    </p>
                </div>

                <span
                    class="inline-flex w-fit rounded-full bg-violet-100 px-3 py-2 text-xs font-semibold text-violet-700 dark:bg-violet-950 dark:text-violet-300"
                >
                    {{ number_format(
                        $levelProgressPercentage,
                        2,
                        ',',
                        '.'
                    ) }}%
                </span>
            </div>

            <div
                class="mt-5 h-3 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
            >
                <div
                    class="h-full rounded-full bg-violet-600 transition-all"
                    style="width: {{ min(
                        100,
                        max(0, $levelProgressPercentage)
                    ) }}%"
                ></div>
            </div>

            <div
                class="mt-3 flex items-center justify-between text-xs text-neutral-500 dark:text-neutral-400"
            >
                <span>
                    {{ number_format(
                        $currentLevelXp,
                        0,
                        ',',
                        '.'
                    ) }} XP
                </span>

                <span>
                    @if ($nextLevelXp === null)
                        Level maksimum
                    @else
                        {{ number_format(
                            $nextLevelXp,
                            0,
                            ',',
                            '.'
                        ) }} XP
                    @endif
                </span>
            </div>
        </section>

        {{-- Penyelesaian seluruh misi --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Progress Misi
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Penyelesaian Misi Pembelajaran
                    </h2>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                    >
                        Kamu telah menyelesaikan
                        {{ $completedMissions }} dari
                        {{ $totalPublishedMissions }} misi yang tersedia.
                    </p>
                </div>

                <span
                    class="inline-flex w-fit rounded-full bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                >
                    {{ number_format(
                        $missionProgressPercentage,
                        2,
                        ',',
                        '.'
                    ) }}%
                </span>
            </div>

            <div
                class="mt-5 h-3 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
            >
                <div
                    class="h-full rounded-full bg-blue-600 transition-all"
                    style="width: {{ min(
                        100,
                        max(0, $missionProgressPercentage)
                    ) }}%"
                ></div>
            </div>
        </section>

        {{-- Riwayat penyelesaian misi --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Riwayat Aktivitas
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Misi yang Telah Diselesaikan
            </h2>

            <p
                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
            >
                Daftar misi yang berhasil kamu tuntaskan beserta XP
                yang diperoleh.
            </p>

            @if ($completions->isEmpty())
                <div
                    class="mt-5 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada misi yang diselesaikan
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Selesaikan seluruh tahap dalam sebuah misi untuk
                        memperoleh XP.
                    </p>

                    <a
                        href="{{ route('student.missions.index') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Lihat Misi Pembelajaran
                    </a>
                </div>
            @else
                <div class="mt-5 space-y-4">
                    @foreach ($completions as $completion)
                        <article
                            class="flex flex-col gap-4 rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                >
                                    ✓
                                </div>

                                <div>
                                    <h3
                                        class="font-bold text-neutral-950 dark:text-white"
                                    >
                                        {{ $completion->mission->title }}
                                    </h3>

                                    <p
                                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                    >
                                        Diselesaikan pada
                                        {{ $completion->completed_at
                                            ->locale('id')
                                            ->translatedFormat(
                                                'd F Y, H:i'
                                            ) }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-3 sm:justify-end"
                            >
                                <span
    class="rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
>
    +{{ number_format($completion->earned_xp, 0, ',', '.') }} XP
</span>

                                <a
                                    href="{{ route(
                                        'student.missions.show',
                                        [
                                            'mission' =>
                                                $completion->mission->slug,
                                        ]
                                    ) }}"
                                    class="rounded-xl border border-neutral-300 px-3 py-2 text-xs font-semibold text-neutral-700 transition hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                                >
                                    Lihat Misi
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
