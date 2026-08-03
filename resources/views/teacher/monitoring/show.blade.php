<x-layouts::app :title="__('Detail Aktivitas Peserta Didik')">
    @php
        $requiredMaterialBlocks =
            $requiredMaterialBlocks ?? collect();

        $materialProgresses =
            $materialProgresses ?? collect();

        $userBadges = $userBadges ?? collect();

        $requiredMaterialBlockCount =
            $requiredMaterialBlockCount ?? 0;

        $completedMaterialBlockCount =
            $completedMaterialBlockCount ?? 0;

        $materialProgressPercentage =
            $materialProgressPercentage ?? 0;

        $totalXp = $totalXp ?? 0;

        $completedMissionCount =
            $completedMissionCount ?? 0;

        $isReady = $isReady ?? false;

        $posttestAttempt =
    $posttestAttempt ?? null;

$learningGain =
    $learningGain ?? null;

        $formatDuration = function (int $seconds): string {
            if ($seconds <= 0) {
                return '0 detik';
            }

            $hours = intdiv($seconds, 3600);
            $minutes = intdiv($seconds % 3600, 60);
            $remainingSeconds = $seconds % 60;

            if ($hours > 0) {
                return "{$hours} jam {$minutes} menit";
            }

            if ($minutes > 0) {
                return "{$minutes} menit {$remainingSeconds} detik";
            }

            return "{$remainingSeconds} detik";
        };
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

                            Detail Monitoring
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Detail Aktivitas Peserta Didik
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Rincian kesiapan prakelas, aktivitas membaca,
                            hasil asesmen, XP, misi, dan badge peserta didik.
                        </p>
                    </div>

                    <a
                        href="{{ route('teacher.monitoring.index') }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                    >
                        <span>←</span>
                        Kembali ke Monitoring
                    </a>
                </div>
            </div>
        </section>

        {{-- Identitas siswa --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-xl font-bold text-white shadow-sm"
                    >
                        {{ strtoupper(
                            mb_substr(
                                $student->name ?? 'SQ',
                                0,
                                2
                            )
                        ) }}
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                        >
                            Identitas Peserta Didik
                        </p>

                        <h2
                            class="mt-1 text-xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $student->name }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            {{ $student->email }}
                        </p>

                        <p
                            class="mt-2 text-xs text-neutral-400 dark:text-neutral-500"
                        >
                            Aktivitas terakhir:

                            @if ($lastActivityAt)
                                {{ $lastActivityAt
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

                @if ($focusedMission === null)
    <span
        class="inline-flex w-fit items-center gap-2 rounded-full bg-neutral-100 px-4 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
    >
        <span>—</span>
        Belum Ada Misi
    </span>
@elseif ($isReady)
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
            </div>
        </section>

        {{-- Ringkasan pencapaian --}}
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Total XP
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ number_format($totalXp, 0, ',', '.') }}

                    <span class="text-lg text-amber-600">
                        XP
                    </span>
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Misi Selesai
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $completedMissionCount }}
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Badge Diperoleh
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $userBadges->count() }}
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Progress Materi
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ number_format(
                        $materialProgressPercentage,
                        0,
                        ',',
                        '.'
                    ) }}%
                </p>

                <div
                    class="mt-4 h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
                >
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500"
                        style="width:
                            {{ min(
                                100,
                                max(
                                    0,
                                    $materialProgressPercentage
                                )
                            ) }}%;"
                    ></div>
                </div>
            </article>
        </section>

        {{-- Misi yang dipantau --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Misi yang Dipantau
            </p>

            <div
                class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2
                        class="text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        {{ $focusedMission?->title
                            ?? 'Belum ada misi dipublikasikan' }}
                    </h2>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ $completedMaterialBlockCount }}
                        dari
                        {{ $requiredMaterialBlockCount }}
                        blok materi wajib telah diselesaikan.
                    </p>
                </div>

                @if ($focusedMission === null)
    <span
        class="w-fit rounded-full bg-neutral-100 px-4 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
    >
        Belum Ada Misi
    </span>
@elseif ($missionCompletion)
    <span
        class="w-fit rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
    >
        Misi Selesai ·
        +{{ number_format(
            $missionCompletion->earned_xp,
            0,
            ',',
            '.'
        ) }}
        XP
    </span>
@else
    <span
        class="w-fit rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
    >
        Misi Belum Selesai
    </span>
@endif
            </div>
        </section>

        {{-- Hasil aktivitas --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Hasil Pembelajaran
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Rekap Hasil Aktivitas
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Nilai terbaru yang tercatat pada setiap tahap
                pembelajaran.
            </p>

            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                {{-- Pretest --}}
                <article
                    class="rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-800 dark:bg-blue-950/40"
                >
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                        >
                            📝
                        </div>

                        @if ($pretestAttempt)
                            <span
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Selesai
                            </span>
                        @else
                            <span
                                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                            >
                                Belum
                            </span>
                        @endif
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Pretest
                    </p>

                    @if ($pretestAttempt)
                        <p
                            class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ number_format(
                                (float) $pretestAttempt->score,
                                0,
                                ',',
                                '.'
                            ) }}

                            <span class="text-base text-blue-600">
                                / 100
                            </span>
                        </p>

                        <p
                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                        >
                            {{ $pretestAttempt->correct_answers }}
                            benar dari
                            {{ $pretestAttempt->total_questions }}
                            soal
                        </p>
                    @else
                        <p
                            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Belum ada hasil Pretest.
                        </p>
                    @endif
                </article>

                {{-- Latihan Mikro --}}
                <article
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-800 dark:bg-emerald-950/40"
                >
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                        >
                            ⚡
                        </div>

                        @if ($microExerciseAttempt)
                            <span
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Selesai
                            </span>
                        @else
                            <span
                                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                            >
                                Belum
                            </span>
                        @endif
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-emerald-600"
                    >
                        Latihan Mikro
                    </p>

                    @if ($microExerciseAttempt)
                        <p
                            class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ number_format(
                                (float) $microExerciseAttempt->score,
                                0,
                                ',',
                                '.'
                            ) }}

                            <span class="text-base text-emerald-600">
                                / 100
                            </span>
                        </p>

                        <p
                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                        >
                            {{ $microExerciseAttempt->correct_answers }}
                            benar dari
                            {{ $microExerciseAttempt->total_questions }}
                            soal
                        </p>

                        <div
                            class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-neutral-900/60"
                        >
                            +{{ number_format(
                                $microExerciseAttempt->earned_points,
                                0,
                                ',',
                                '.'
                            ) }}
                            poin
                        </div>
                    @else
                        <p
                            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Belum ada hasil Latihan Mikro.
                        </p>
                    @endif
                </article>

                {{-- Kuis Materi --}}
                <article
                    class="rounded-2xl border border-violet-200 bg-violet-50 p-5 dark:border-violet-800 dark:bg-violet-950/40"
                >
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                        >
                            ❓
                        </div>

                        @if ($quizAttempt)
                            <span
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Selesai
                            </span>
                        @else
                            <span
                                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                            >
                                Belum
                            </span>
                        @endif
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Kuis Materi
                    </p>

                    @if ($quizAttempt)
                        <p
                            class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ number_format(
                                (float) $quizAttempt->score,
                                0,
                                ',',
                                '.'
                            ) }}

                            <span class="text-base text-violet-600">
                                / 100
                            </span>
                        </p>

                        <p
                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                        >
                            {{ $quizAttempt->correct_answers }}
                            benar dari
                            {{ $quizAttempt->total_questions }}
                            soal
                        </p>

                        <div
                            class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-violet-700 dark:bg-neutral-900/60"
                        >
                            +{{ number_format(
                                $quizAttempt->earned_points,
                                0,
                                ',',
                                '.'
                            ) }}
                            poin
                        </div>
                    @else
                        <p
                            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Belum ada hasil Kuis Materi.
                        </p>
                    @endif
                </article>

                {{-- Final Challenge --}}
                <article
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950/40"
                >
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
                        >
                            🏆
                        </div>

                        @if ($finalChallengeAttempt)
                            <span
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Selesai
                            </span>
                        @else
                            <span
                                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                            >
                                Belum
                            </span>
                        @endif
                    </div>

                    <p
                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-amber-600"
                    >
                        Final Challenge
                    </p>

                    @if ($finalChallengeAttempt)
                        <p
                            class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ number_format(
                                (float) $finalChallengeAttempt->score,
                                0,
                                ',',
                                '.'
                            ) }}

                            <span class="text-base text-amber-600">
                                / 100
                            </span>
                        </p>

                        <div
                            class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-neutral-900/60"
                        >
                            +{{ number_format(
                                $finalChallengeAttempt->earned_points,
                                0,
                                ',',
                                '.'
                            ) }}
                            poin
                        </div>

                        @if ($finalChallengeAttempt->feedback)
                            <p
                                class="mt-3 text-xs leading-5 text-neutral-600 dark:text-neutral-300"
                            >
                                {{ $finalChallengeAttempt->feedback }}
                            </p>
                        @endif
                    @else
                        <p
                            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Belum ada hasil Final Challenge.
                        </p>
                    @endif
                </article>

                {{-- Posttest --}}
<article
    class="rounded-2xl border border-cyan-200 bg-cyan-50 p-5 dark:border-cyan-800 dark:bg-cyan-950/40"
>
    <div
        class="flex items-center justify-between gap-3"
    >
        <div
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
        >
            📊
        </div>

        @if ($posttestAttempt)
            <span
                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
            >
                Selesai
            </span>
        @else
            <span
                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
            >
                Belum
            </span>
        @endif
    </div>

    <p
        class="mt-4 text-xs font-semibold uppercase tracking-wider text-cyan-600"
    >
        Posttest
    </p>

    @if ($posttestAttempt)
        <p
            class="mt-2 text-3xl font-bold text-neutral-950 dark:text-white"
        >
            {{ number_format(
                (float) $posttestAttempt->score,
                0,
                ',',
                '.'
            ) }}

            <span class="text-base text-cyan-600">
                / 100
            </span>
        </p>

        <p
            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
        >
            {{ $posttestAttempt->correct_answers }}
            benar dari
            {{ $posttestAttempt->total_questions }}
            soal
        </p>
    @else
        <p
            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
        >
            Belum ada hasil Posttest.
        </p>
    @endif
</article>

{{-- Peningkatan Nilai --}}
<article
    class="rounded-2xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-800 dark:bg-rose-950/40"
>
    <div
        class="flex items-center justify-between gap-3"
    >
        <div
            class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-sm dark:bg-neutral-900"
        >
            📈
        </div>

        @if ($learningGain !== null)
            <span
                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
            >
                Tersedia
            </span>
        @else
            <span
                class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
            >
                Belum
            </span>
        @endif
    </div>

    <p
        class="mt-4 text-xs font-semibold uppercase tracking-wider text-rose-600"
    >
        Peningkatan Nilai
    </p>

    @if ($learningGain !== null)
        <p
            class="mt-2 text-3xl font-bold
                {{ $learningGain >= 0
                    ? 'text-emerald-600'
                    : 'text-rose-600' }}"
        >
            {{ $learningGain > 0 ? '+' : '' }}

            {{ number_format(
                $learningGain,
                0,
                ',',
                '.'
            ) }}

            <span class="text-base">
                poin
            </span>
        </p>

        <p
            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
        >
            Selisih nilai Posttest terhadap Pretest.
        </p>
    @else
        <p
            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
        >
            Belum dapat dihitung karena hasil Pretest
            atau Posttest belum tersedia.
        </p>
    @endif
</article>

            </div>
        </section>

        {{-- Ringkasan membaca --}}
        <section class="grid gap-4 md:grid-cols-2">
            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Durasi Membaca
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $formattedReadTime }}
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Akumulasi waktu membaca seluruh blok materi wajib.
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Kunjungan Materi
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $totalVisits }}
                    <span class="text-lg text-violet-600">
                        kali
                    </span>
                </p>

                <p
                    class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Total pembukaan seluruh blok materi wajib.
                </p>
            </article>
        </section>

        {{-- Detail blok materi --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Monitoring Membaca
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Detail Aktivitas Setiap Blok Materi
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Informasi kunjungan, durasi baca, dan status penyelesaian
                setiap blok materi wajib.
            </p>

            @if ($requiredMaterialBlocks->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada blok materi wajib
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($requiredMaterialBlocks as $index => $block)
                        @php
                            $progress =
                                $materialProgresses->get($block->id);

                            $readSeconds = (int) (
                                $progress?->total_read_seconds
                                ?? 0
                            );

                            $minimumSeconds = (int) (
                                $block->minimum_read_seconds
                                ?? 0
                            );

                            $isCompleted = (bool) (
                                $progress?->is_completed
                                ?? false
                            );
                        @endphp

                        <article
                            class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700"
                        >
                            <div
                                class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div class="flex gap-4">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 font-bold text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                                    >
                                        {{ $index + 1 }}
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                                        >
                                            {{ $block->materialSection?->title
                                                ?? 'Bagian Materi' }}
                                        </p>

                                        <h3
                                            class="mt-1 font-bold text-neutral-950 dark:text-white"
                                        >
                                            {{ $block->title
                                                ?? ucfirst($block->type) }}
                                        </h3>

                                        <p
                                            class="mt-1 text-xs text-neutral-500 dark:text-neutral-400"
                                        >
                                            Jenis blok:
                                            {{ ucfirst($block->type) }}
                                        </p>
                                    </div>
                                </div>

                                @if ($isCompleted)
                                    <span
                                        class="w-fit rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                    >
                                        Selesai
                                    </span>
                                @else
                                    <span
                                        class="w-fit rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                    >
                                        Belum Selesai
                                    </span>
                                @endif
                            </div>

                            <div
                                class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Waktu Baca
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $formatDuration(
                                            $readSeconds
                                        ) }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Waktu Minimum
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $formatDuration(
                                            $minimumSeconds
                                        ) }}
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Kunjungan
                                    </p>

                                    <p
                                        class="mt-2 font-bold text-neutral-900 dark:text-white"
                                    >
                                        {{ $progress?->visit_count ?? 0 }}
                                        kali
                                    </p>
                                </div>

                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-neutral-400"
                                    >
                                        Aktivitas Terakhir
                                    </p>

                                    <p
                                        class="mt-2 text-sm font-bold text-neutral-900 dark:text-white"
                                    >
                                        @if ($progress?->last_activity_at)
                                            {{ $progress
                                                ->last_activity_at
                                                ->locale('id')
                                                ->translatedFormat(
                                                    'd F Y, H:i'
                                                ) }}
                                        @else
                                            Belum ada
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Badge siswa --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Penghargaan Pembelajaran
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Badge yang Telah Diperoleh
            </h2>

            @if ($userBadges->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada badge yang diperoleh
                    </p>
                </div>
            @else
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    @foreach ($userBadges as $userBadge)
                        @php
                            $badge = $userBadge->badge;

                            $icon = match ($badge->icon) {
                                'flag' => '⚑',
                                'star' => '★',
                                'trophy' => '🏆',
                                default => '◆',
                            };
                        @endphp

                        <article
                            class="rounded-2xl border border-violet-200 bg-violet-50 p-5 dark:border-violet-800 dark:bg-violet-950/40"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-2xl shadow-sm dark:bg-neutral-900"
                            >
                                {{ $icon }}
                            </div>

                            <h3
                                class="mt-4 font-bold text-violet-700 dark:text-violet-300"
                            >
                                {{ $badge->name }}
                            </h3>

                            <p
                                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                            >
                                {{ $badge->description }}
                            </p>

                            <p
                                class="mt-4 text-xs font-semibold text-violet-600 dark:text-violet-300"
                            >
                                Diperoleh pada

                                {{ $userBadge->awarded_at
                                    ->locale('id')
                                    ->translatedFormat(
                                        'd F Y, H:i'
                                    ) }}
                            </p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
