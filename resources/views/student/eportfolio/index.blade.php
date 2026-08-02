<x-layouts::app :title="'E-Portfolio'">
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            html,
            body {
                background: #ffffff !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /*
            * Menyembunyikan navigasi aplikasi pada hasil cetak.
            */
            body > header,
            body > nav,
            body > aside {
                display: none !important;
            }

            .portfolio-print-page {
                gap: 10px !important;
            }

            .portfolio-print-button {
                display: none !important;
            }

            /*
             * Mencegah kartu terpotong di antara dua halaman.
             */
            .portfolio-print-page article {
    box-shadow: none !important;

    /*
 * Memulai seluruh bagian Badge pada halaman baru.
 * Break diterapkan pada pembungkus yang memiliki isi
 * agar tidak diabaikan oleh browser.
 */
.portfolio-print-badges-page {
    display: block !important;
    width: 100% !important;
    overflow: visible !important;
}

.portfolio-print-badges-section {
    display: block !important;
    overflow: visible !important;
    break-inside: avoid !important;
    page-break-inside: avoid !important;
}

.portfolio-print-badges {
    display: grid !important;
    grid-template-columns:
        repeat(3, minmax(0, 1fr)) !important;
    gap: 8px !important;
    overflow: visible !important;
}

.portfolio-print-badges article {
    min-width: 0 !important;
    break-inside: avoid !important;
    page-break-inside: avoid !important;
}
}

            /*
             * Empat bagian awal dibuat tetap utuh:
             * header, identitas, ringkasan, dan perkembangan.
             */
            .portfolio-print-page
                > section:nth-of-type(-n + 4) {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            /*
             * Mempertahankan susunan kartu saat dicetak.
             */
            .portfolio-print-summary {
                grid-template-columns:
                    repeat(4, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .portfolio-print-progress {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .portfolio-print-results {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            .portfolio-print-badges {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr)) !important;
                gap: 8px !important;
            }

            /*
             * Mengurangi jarak dan ukuran agar muat pada kertas A4.
             */
            .portfolio-print-page .p-6 {
                padding: 14px !important;
            }

            .portfolio-print-page .p-5 {
                padding: 12px !important;
            }

            .portfolio-print-page .gap-6 {
                gap: 10px !important;
            }

            .portfolio-print-page .gap-5,
            .portfolio-print-page .gap-4 {
                gap: 8px !important;
            }

            .portfolio-print-page .mt-6 {
                margin-top: 12px !important;
            }

            .portfolio-print-page .mt-5 {
                margin-top: 10px !important;
            }

            .portfolio-print-page .mt-4 {
                margin-top: 8px !important;
            }

            .portfolio-print-page .mt-3 {
                margin-top: 6px !important;
            }

            .portfolio-print-page .mt-2 {
                margin-top: 4px !important;
            }

            .portfolio-print-page .text-3xl {
                font-size: 20px !important;
                line-height: 1.2 !important;
            }

            .portfolio-print-page .text-2xl {
                font-size: 18px !important;
                line-height: 1.25 !important;
            }

            .portfolio-print-page .text-xl {
                font-size: 15px !important;
                line-height: 1.3 !important;
            }

            .portfolio-print-page .text-lg {
                font-size: 13px !important;
                line-height: 1.35 !important;
            }

            .portfolio-print-page .text-sm {
                font-size: 10px !important;
                line-height: 1.4 !important;
            }

            .portfolio-print-page .text-xs {
                font-size: 8px !important;
                line-height: 1.35 !important;
            }

            .portfolio-print-page .h-16,
            .portfolio-print-page .w-16 {
                width: 44px !important;
                height: 44px !important;
            }

            .portfolio-print-page .h-14,
            .portfolio-print-page .w-14 {
                width: 38px !important;
                height: 38px !important;
            }

                        .portfolio-print-page .h-12,
            .portfolio-print-page .w-12 {
                width: 34px !important;
                height: 34px !important;
            }

            /*
             * Bagian Bukti Hasil Belajar selalu dimulai
             * dari halaman baru saat dicetak.
             */
            .portfolio-print-results-section {
    display: block !important;
    break-before: page !important;
    page-break-before: always !important;
    break-after: page !important;
    page-break-after: always !important;
    overflow: visible !important;
}

            /*
             * Satu kelompok hasil misi tidak boleh
             * dipotong di tengah halaman.
             */
            .portfolio-print-results-section article {
                display: block !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                overflow: visible !important;
            }

            /*
             * Empat kartu nilai disusun menjadi dua kolom.
             */
            .portfolio-print-results {
                display: grid !important;
                grid-template-columns:
                    repeat(2, minmax(0, 1fr)) !important;
                gap: 8px !important;
                align-items: stretch !important;
                overflow: visible !important;
            }

            /*
             * Setiap kartu nilai tidak boleh terpotong.
             */
            .portfolio-print-results > * {
                width: auto !important;
                min-width: 0 !important;
                max-width: none !important;
                margin: 0 !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
        }
    </style>

    <div class="quest-page portfolio-print-page">
        {{-- Header E-Portfolio --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <div
                    class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-violet-500"
                            ></span>

                            Portofolio Pembelajaran
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            E-Portfolio Peserta Didik
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Dokumentasi perjalanan belajar, pencapaian,
                            penyelesaian misi, perolehan XP, dan badge
                            selama menggunakan Questify.
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="portfolio-print-button inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        <span>▣</span>
                        Cetak Portofolio
                    </button>
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
                            mb_substr($user->name ?? 'SQ', 0, 2)
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
                            {{ $user->name ?? 'Siswa Questify' }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            {{ $user->email }}
                        </p>
                    </div>
                </div>

                <span
                    class="w-fit rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    Peserta Didik Aktif
                </span>
            </div>
        </section>

        {{-- Ringkasan pencapaian --}}
        <section
    class="portfolio-print-summary grid gap-4 md:grid-cols-2 xl:grid-cols-4"
>
            <div class="quest-card p-6">
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
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Level Saat Ini
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $level }}
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
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Badge Diperoleh
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $earnedBadgeCount }}

                    <span class="text-lg text-violet-600">
                        dari {{ $totalActiveBadges }}
                    </span>
                </p>
            </div>
        </section>

        {{-- Ringkasan perkembangan --}}
        <section
    class="portfolio-print-progress grid gap-4 lg:grid-cols-2"
>
            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Perkembangan Misi
                </p>

                <div class="mt-3 flex items-end justify-between gap-4">
                    <div>
                        <p
                            class="text-3xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ number_format(
                                $missionProgressPercentage,
                                0,
                                ',',
                                '.'
                            ) }}%
                        </p>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Penyelesaian seluruh misi yang tersedia
                        </p>
                    </div>

                    <span
                        class="rounded-full bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                    >
                        {{ $completedMissions }} misi selesai
                    </span>
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
                                    $missionProgressPercentage
                                )
                            ) }}%;"
                    ></div>
                </div>
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-orange-600"
                >
                    Pencapaian Final Challenge
                </p>

                <div class="mt-3 flex items-end justify-between gap-4">
                    <div>
                        <p
                            class="text-3xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ number_format(
                                $highestChallengeScore,
                                0,
                                ',',
                                '.'
                            ) }}

                            <span class="text-lg text-orange-600">
                                / 100
                            </span>
                        </p>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Nilai Final Challenge tertinggi
                        </p>
                    </div>

                    @if ($highestChallengeScore >= 100)
                        <span
                            class="rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                        >
                            Sempurna
                        </span>
                    @elseif ($highestChallengeScore > 0)
                        <span
                            class="rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                        >
                            Telah Dikerjakan
                        </span>
                    @else
                        <span
                            class="rounded-full bg-neutral-100 px-3 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                        >
                            Belum Ada
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Riwayat misi --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Bukti Perjalanan Belajar
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Misi yang Telah Diselesaikan
            </h2>

            <p
                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
            >
                Misi yang tercatat pada E-Portfolio merupakan misi yang
                seluruh tahap pembelajarannya telah diselesaikan.
            </p>

            @if ($completions->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada misi yang diselesaikan
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Selesaikan seluruh tahap misi agar hasilnya
                        tercatat dalam E-Portfolio.
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($completions as $index => $completion)
                        <article
                            class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 via-white to-violet-50 p-5 dark:border-blue-900 dark:from-blue-950 dark:via-neutral-900 dark:to-violet-950"
                        >
                            <div
                                class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between"
                            >
                                <div class="flex gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 font-bold text-white"
                                    >
                                        {{ $index + 1 }}
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-300"
                                        >
                                            Misi Pembelajaran
                                        </p>

                                        <h3
                                            class="mt-2 text-lg font-bold text-neutral-950 dark:text-white"
                                        >
                                            {{ $completion->mission?->title
                                                ?? 'Misi Questify' }}
                                        </h3>

                                        <p
                                            class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                                        >
                                            Diselesaikan pada

                                            {{ \Illuminate\Support\Carbon::parse(
                                                $completion->completed_at
                                            )
                                                ->locale('id')
                                                ->translatedFormat(
                                                    'd F Y, H:i'
                                                ) }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="w-fit rounded-full bg-amber-100 px-4 py-2 text-sm font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                >
                                    +{{ number_format(
                                        $completion->earned_xp,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                    XP
                                </div>
                            </div>

                            <div class="mt-5">
                                <div
                                    class="mb-2 flex items-center justify-between text-xs font-semibold"
                                >
                                    <span
                                        class="text-neutral-600 dark:text-neutral-300"
                                    >
                                        Progress Misi
                                    </span>

                                    <span class="text-emerald-600">
                                        100% Selesai
                                    </span>
                                </div>

                                <div
                                    class="h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700"
                                >
                                    <div
                                        class="h-full w-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500"
                                    ></div>
                                </div>
                            </div>

                            <div
                                class="mt-5 flex flex-wrap gap-2"
                            >
                                @foreach ([
    'Pretest',
    'Materi Prakelas',
    'Latihan Mikro',
    'Kuis Materi',
    'Final Challenge',
    'Posttest',
] as $stage)
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300"
                                    >
                                        <span
                                            class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-[10px] text-white"
                                        >
                                            ✓
                                        </span>

                                        {{ $stage }}
                                    </span>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

{{-- Bukti hasil belajar --}}

<section class="quest-card p-6 portfolio-print-results-section">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
            >
                Rekapitulasi Pencapaian
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Bukti Hasil Belajar
            </h2>

            <p
                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
            >
                Bagian ini menampilkan hasil Pretest, Latihan Mikro,
Kuis Materi, Final Challenge, Posttest, serta peningkatan
nilai pada setiap misi yang telah diselesaikan.
            </p>

            @if ($completions->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada hasil belajar
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Hasil aktivitas akan tampil setelah misi
                        pembelajaran diselesaikan.
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-6">
                    @foreach ($completions as $completion)
                        @php
    $missionId = $completion->mission_id;

    $pretestAttempt =
        $pretestAttempts->get($missionId);

    $posttestAttempt =
        $posttestAttempts->get($missionId);

    $microExerciseAttempt =
        $microExerciseAttempts->get($missionId);

    $quizAttempt =
        $quizAttempts->get($missionId);

    $finalChallengeAttempt =
        $finalChallengeAttempts->get($missionId);

    /*
     * Menghitung selisih nilai Posttest dan Pretest.
     * Nilai positif berarti meningkat.
     * Nilai nol berarti tetap.
     * Nilai negatif berarti menurun.
     */
    $scoreImprovement = null;

    if ($pretestAttempt && $posttestAttempt) {
        $scoreImprovement =
            (float) $posttestAttempt->score
            - (float) $pretestAttempt->score;
    }
@endphp

                        <article
                            class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700"
                        >
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                                    >
                                        Hasil Misi
                                    </p>

                                    <h3
                                        class="mt-2 text-lg font-bold text-neutral-950 dark:text-white"
                                    >
                                        {{ $completion->mission?->title
                                            ?? 'Misi Questify' }}
                                    </h3>
                                </div>

                                <span
                                    class="w-fit rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                >
                                    Seluruh Tahap Selesai
                                </span>
                            </div>

                            <div
                               class="portfolio-print-results mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5"
                            >
                                {{-- Pretest --}}
                                <div
                                    class="rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-800 dark:bg-blue-950"
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
                                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
                                            >
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-neutral-200 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                            >
                                                Belum Ada
                                            </span>
                                        @endif
                                    </div>

                                    <p
                                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-300"
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

                                            <span
                                                class="text-base text-blue-600 dark:text-blue-300"
                                            >
                                                / 100
                                            </span>
                                        </p>

                                        <p
                                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                                        >
                                            {{ $pretestAttempt->correct_answers }}
                                            jawaban benar dari
                                            {{ $pretestAttempt->total_questions }}
                                            soal
                                        </p>

                                        <p
                                            class="mt-4 text-xs text-neutral-500 dark:text-neutral-400"
                                        >
                                            Diselesaikan
                                            {{ $pretestAttempt->completed_at
                                                ->locale('id')
                                                ->translatedFormat(
                                                    'd F Y, H:i'
                                                ) }}
                                        </p>
                                    @else
                                        <p
                                            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                                        >
                                            Data Pretest belum tersedia.
                                        </p>
                                    @endif
                                </div>

                                {{-- Latihan Mikro --}}
                                <div
                                    class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-800 dark:bg-emerald-950"
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
                                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
                                            >
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-neutral-200 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                            >
                                                Belum Ada
                                            </span>
                                        @endif
                                    </div>

                                    <p
                                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-300"
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

                                            <span
                                                class="text-base text-emerald-600 dark:text-emerald-300"
                                            >
                                                / 100
                                            </span>
                                        </p>

                                        <p
                                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                                        >
                                            {{ $microExerciseAttempt->correct_answers }}
                                            jawaban benar dari
                                            {{ $microExerciseAttempt->total_questions }}
                                            soal
                                        </p>

                                        <div
                                            class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-neutral-900/60 dark:text-emerald-300"
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
                                            Data Latihan Mikro belum tersedia.
                                        </p>
                                    @endif
                                </div>

                                {{-- Kuis Materi --}}
                                <div
                                    class="rounded-2xl border border-violet-200 bg-violet-50 p-5 dark:border-violet-800 dark:bg-violet-950"
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
                                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
                                            >
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-neutral-200 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                            >
                                                Belum Ada
                                            </span>
                                        @endif
                                    </div>

                                    <p
                                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-violet-600 dark:text-violet-300"
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

                                            <span
                                                class="text-base text-violet-600 dark:text-violet-300"
                                            >
                                                / 100
                                            </span>
                                        </p>

                                        <p
                                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                                        >
                                            {{ $quizAttempt->correct_answers }}
                                            jawaban benar dari
                                            {{ $quizAttempt->total_questions }}
                                            soal
                                        </p>

                                        <div
                                            class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-violet-700 dark:bg-neutral-900/60 dark:text-violet-300"
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
                                            Data Kuis Materi belum tersedia.
                                        </p>
                                    @endif
                                </div>

                                {{-- Final Challenge --}}
                                <div
                                    class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-800 dark:bg-amber-950"
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
                                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
                                            >
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-neutral-200 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                            >
                                                Belum Ada
                                            </span>
                                        @endif
                                    </div>

                                    <p
                                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-300"
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

                                            <span
                                                class="text-base text-amber-600 dark:text-amber-300"
                                            >
                                                / 100
                                            </span>
                                        </p>

                                        <div
                                            class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-neutral-900/60 dark:text-amber-300"
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
                                            Data Final Challenge belum tersedia.
                                        </p>
                                    @endif
                                </div>

                                                                {{-- Posttest --}}
                                <div
                                    class="rounded-2xl border border-cyan-200 bg-cyan-50 p-5 dark:border-cyan-800 dark:bg-cyan-950"
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
                                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
                                            >
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-neutral-200 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                            >
                                                Belum Ada
                                            </span>
                                        @endif
                                    </div>

                                    <p
                                        class="mt-4 text-xs font-semibold uppercase tracking-wider text-cyan-600 dark:text-cyan-300"
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

                                            <span
                                                class="text-base text-cyan-600 dark:text-cyan-300"
                                            >
                                                / 100
                                            </span>
                                        </p>

                                        <p
                                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                                        >
                                            {{ $posttestAttempt->correct_answers }}
                                            jawaban benar dari
                                            {{ $posttestAttempt->total_questions }}
                                            soal
                                        </p>

                                        <p
                                            class="mt-4 text-xs text-neutral-500 dark:text-neutral-400"
                                        >
                                            Diselesaikan
                                            {{ $posttestAttempt->completed_at
                                                ->locale('id')
                                                ->translatedFormat(
                                                    'd F Y, H:i'
                                                ) }}
                                        </p>
                                    @else
                                        <p
                                            class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                                        >
                                            Data Posttest belum tersedia.
                                        </p>
                                    @endif
                                </div>
                                </div>

                                                            {{-- Peningkatan nilai Pretest–Posttest --}}
                            <div
                                class="mt-5 rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 via-white to-emerald-50 p-5 dark:border-indigo-800 dark:from-indigo-950 dark:via-neutral-900 dark:to-emerald-950"
                            >
                                <div
                                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-300"
                                        >
                                            Perkembangan Hasil Belajar
                                        </p>

                                        <h4
                                            class="mt-2 text-lg font-bold text-neutral-950 dark:text-white"
                                        >
                                            Peningkatan Nilai Pretest–Posttest
                                        </h4>

                                        <p
                                            class="mt-2 text-sm text-neutral-600 dark:text-neutral-300"
                                        >
                                            Selisih dihitung dari nilai
                                            Posttest dikurangi nilai Pretest.
                                        </p>
                                    </div>

                                    @if ($scoreImprovement !== null)
                                        <div class="sm:text-right">
                                            <p
                                                class="text-3xl font-bold
                                                    @if ($scoreImprovement > 0)
                                                        text-emerald-600
                                                    @elseif ($scoreImprovement < 0)
                                                        text-red-600
                                                    @else
                                                        text-amber-600
                                                    @endif"
                                            >
                                                @if ($scoreImprovement > 0)
                                                    +
                                                @endif

                                                {{ number_format(
                                                    $scoreImprovement,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                                <span class="text-base">
                                                    poin
                                                </span>
                                            </p>

                                            @if ($scoreImprovement > 0)
                                                <span
                                                    class="mt-2 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                                >
                                                    Nilai Meningkat
                                                </span>
                                            @elseif ($scoreImprovement < 0)
                                                <span
                                                    class="mt-2 inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-300"
                                                >
                                                    Nilai Menurun
                                                </span>
                                            @else
                                                <span
                                                    class="mt-2 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                                >
                                                    Nilai Tetap
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span
                                            class="w-fit rounded-full bg-neutral-100 px-3 py-2 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300"
                                        >
                                            Belum Dapat Dihitung
                                        </span>
                                    @endif
                                </div>

                                @if (
                                    $pretestAttempt
                                    && $posttestAttempt
                                )
                                    <div
                                        class="mt-5 grid gap-3 sm:grid-cols-3"
                                    >
                                        <div
                                            class="rounded-xl bg-white/80 p-4 dark:bg-neutral-900/70"
                                        >
                                            <p
                                                class="text-xs font-semibold text-neutral-500 dark:text-neutral-400"
                                            >
                                                Nilai Pretest
                                            </p>

                                            <p
                                                class="mt-1 text-xl font-bold text-blue-600"
                                            >
                                                {{ number_format(
                                                    (float) $pretestAttempt->score,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl bg-white/80 p-4 dark:bg-neutral-900/70"
                                        >
                                            <p
                                                class="text-xs font-semibold text-neutral-500 dark:text-neutral-400"
                                            >
                                                Nilai Posttest
                                            </p>

                                            <p
                                                class="mt-1 text-xl font-bold text-cyan-600"
                                            >
                                                {{ number_format(
                                                    (float) $posttestAttempt->score,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl bg-white/80 p-4 dark:bg-neutral-900/70"
                                        >
                                            <p
                                                class="text-xs font-semibold text-neutral-500 dark:text-neutral-400"
                                            >
                                                Selisih Nilai
                                            </p>

                                            <p
                                                class="mt-1 text-xl font-bold text-indigo-600"
                                            >
                                                @if ($scoreImprovement > 0)
                                                    +
                                                @endif

                                                {{ number_format(
                                                    $scoreImprovement,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>


<div class="portfolio-print-badges-page">
    {{-- Badge yang diperoleh --}}
    <section
        class="quest-card p-6 portfolio-print-badges-section"
    >
                Penghargaan Pembelajaran
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Badge yang Telah Diperoleh
            </h2>

            <p
                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
            >
                Badge menjadi bukti pencapaian peserta didik selama
                menyelesaikan aktivitas dan tantangan Questify.
            </p>

            @if ($userBadges->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada badge yang diperoleh
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Selesaikan misi dan kumpulkan XP untuk
                        memperoleh badge.
                    </p>
                </div>
            @else
                <div class="portfolio-print-badges mt-6 grid gap-5 md:grid-cols-3">
                    @foreach ($userBadges as $userBadge)
                        @php
                            $badge = $userBadge->badge;

                            $themeClasses = match ($badge->theme) {
                                'amber' =>
                                    'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-300',
                                'violet' =>
                                    'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300',
                                default =>
                                    'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300',
                            };

                            $icon = match ($badge->icon) {
                                'flag' => '⚑',
                                'star' => '★',
                                'trophy' => '🏆',
                                default => '◆',
                            };
                        @endphp

                        <article
                            class="rounded-2xl border p-5 {{ $themeClasses }}"
                        >
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm dark:bg-neutral-900"
                            >
                                {{ $icon }}
                            </div>

                            <p
                                class="mt-5 text-xs font-semibold uppercase tracking-wider"
                            >
                                Telah Diperoleh
                            </p>

                            <h3 class="mt-2 text-lg font-bold">
                                {{ $badge->name }}
                            </h3>

                            <p class="mt-2 text-sm leading-6">
                                {{ $badge->description }}
                            </p>

                            <div
                                class="mt-5 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold dark:bg-neutral-900/60"
                            >
                                Diperoleh pada

                                {{ $userBadge->awarded_at
                                    ->locale('id')
                                    ->translatedFormat(
                                        'd F Y, H:i'
                                    ) }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
</div>
    </div>
</x-layouts::app>
