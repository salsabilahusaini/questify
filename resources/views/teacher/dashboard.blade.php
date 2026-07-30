<x-layouts::app :title="__('Dashboard Guru')">
    @php
        /*
         * Nilai default mencegah halaman error apabila salah satu
         * data belum dikirimkan oleh controller.
         */
        $studentCount = $studentCount ?? 0;
        $activeModuleCount = $activeModuleCount ?? 0;
        $readyStudentCount = $readyStudentCount ?? 0;
        $attentionStudentCount = $attentionStudentCount ?? 0;
        $focusedMission = $focusedMission ?? null;

$pretestCompletedCount =
    $pretestCompletedCount ?? 0;

$materialCompletedCount =
    $materialCompletedCount ?? 0;

$microExerciseCompletedCount =
    $microExerciseCompletedCount ?? 0;

$quizCompletedCount =
    $quizCompletedCount ?? 0;

$requiredMaterialBlockCount =
    $requiredMaterialBlockCount ?? 0;

        $readinessPercentage = $studentCount > 0
            ? (int) round(($readyStudentCount / $studentCount) * 100)
            : 0;
    @endphp

    <div class="quest-page">

        {{-- Hero Dashboard Guru --}}
        <section class="quest-hero">
            <div
                class="relative z-10 flex flex-col gap-7 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        DASHBOARD GURU
                    </div>

                    <h1
                        class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white"
                    >
                        Selamat datang,
                        <span
                            class="bg-gradient-to-r from-blue-500 via-indigo-500 to-emerald-500 bg-clip-text text-transparent"
                        >
                            {{ auth()->user()->name }}!
                        </span>
                    </h1>

                    <p
                        class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300"
                    >
                        Pantau kesiapan belajar, perkembangan misi, serta hasil
                        pembelajaran peserta didik dalam satu ruang kerja
                        Questify.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a
                            href="#alur-pembelajaran"
                            class="quest-button-primary"
                        >
                            <svg
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5" />
                                <path d="M6 17h12" />
                                <path d="M8 7h8M8 11h6" />
                            </svg>

                            Lihat Alur Pembelajaran
                        </a>

                        <span class="quest-badge">
                            Blended Learning Workspace
                        </span>
                    </div>
                </div>

                {{-- Dekorasi Hero --}}
                <div class="relative hidden h-44 w-60 shrink-0 lg:block">
                    <div
                        class="absolute right-2 top-2 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-emerald-400 to-blue-700 shadow-2xl shadow-blue-500/30"
                    ></div>

                    <div
                        class="absolute bottom-1 left-3 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/40 bg-white/80 shadow-xl backdrop-blur dark:bg-neutral-900/80"
                    >
                        <svg
                            class="size-14 text-blue-600 dark:text-blue-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="m3 10 9-5 9 5-9 5-9-5Z" />
                            <path d="m7 12.5v4.2c0 1.3 2.2 2.3 5 2.3s5-1 5-2.3v-4.2" />
                            <path d="M21 10v6" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Statistik --}}
        <section>
            <div
                class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <h2
                        class="text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Ringkasan Pembelajaran
                    </h2>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Informasi awal aktivitas peserta didik dan modul
                        pembelajaran.
                    </p>
                </div>

                <span
                    class="text-xs font-medium text-neutral-400 dark:text-neutral-500"
                >
                    Diperbarui otomatis
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Peserta Didik --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                            >
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" />
                                </svg>
                            </div>

                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                            >
                                Pembelajar
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Peserta Didik
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $studentCount }}
                        </p>

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Terdaftar pada platform
                        </p>
                    </div>
                </article>

                {{-- Modul Aktif --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-300"
                            >
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5" />
                                    <path d="M6 17h12" />
                                    <path d="M8 7h8M8 11h6" />
                                </svg>
                            </div>

                            <span
                                class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-600 dark:bg-violet-950 dark:text-violet-300"
                            >
                                Konten
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Modul Aktif
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $activeModuleCount }}
                        </p>

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Modul siap digunakan
                        </p>
                    </div>
                </article>

                {{-- Siap Belajar --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"
                            >
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

                            <span
                                class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Prakelas
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Siap Belajar
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $readyStudentCount }}
                        </p>

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Menyelesaikan kegiatan prakelas
                        </p>
                    </div>
                </article>

                {{-- Perlu Perhatian --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-300"
                            >
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M12 9v4" />
                                    <path d="M12 17h.01" />
                                    <path d="M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z" />
                                </svg>
                            </div>

                            <span
                                class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-950 dark:text-amber-300"
                            >
                                Monitoring
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Perlu Perhatian
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $attentionStudentCount }}
                        </p>

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Aktivitas belum lengkap
                        </p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Kesiapan dan Prioritas --}}
        <section class="grid gap-5 xl:grid-cols-[1.35fr_0.65fr]">

            {{-- Kesiapan Belajar --}}
            <article class="quest-card p-6 sm:p-7">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div>
                        <span class="quest-eyebrow">
                            PRE-CLASS READINESS
                        </span>

                        <h2
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Kesiapan Belajar Peserta Didik
                        </h2>

                        <p
                            class="mt-2 max-w-2xl text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                        >
                            Persentase peserta didik yang telah menyelesaikan
                            rangkaian kegiatan prakelas sebelum pembelajaran
                            tatap muka.
                        </p>

                        @if ($focusedMission)
    <div
        class="mt-3 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300"
    >
        <span
            class="h-2 w-2 rounded-full bg-blue-500"
        ></span>

        Misi dipantau:
        {{ $focusedMission->title }}
    </div>
@endif
                    </div>

                    <div
                        class="flex size-20 shrink-0 items-center justify-center rounded-full border-8 border-blue-100 bg-white text-xl font-bold text-blue-600 shadow-sm dark:border-blue-950 dark:bg-neutral-900 dark:text-blue-300"
                    >
                        {{ $readinessPercentage }}%
                    </div>
                </div>

                <div class="mt-7">
                    <div
                        class="mb-2 flex items-center justify-between text-sm"
                    >
                        <span
                            class="font-medium text-neutral-600 dark:text-neutral-300"
                        >
                            Progres kesiapan
                        </span>

                        <span
                            class="font-bold text-blue-600 dark:text-blue-300"
                        >
                            {{ $readyStudentCount }} dari
                            {{ $studentCount }} peserta didik
                        </span>
                    </div>

                    <div
                        class="h-3 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800"
                    >
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-blue-500 to-emerald-500 transition-all duration-500"
                            style="width: {{ $readinessPercentage }}%"
                        ></div>
                    </div>
                </div>

                <div
    class="mt-7 grid gap-3 sm:grid-cols-2 xl:grid-cols-4"
>
    {{-- Penyelesaian Pretest --}}
    <div
        class="rounded-2xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40"
    >
        <div
            class="flex items-start justify-between gap-3"
        >
            <div
                class="flex size-9 items-center justify-center rounded-xl bg-white text-lg shadow-sm dark:bg-neutral-900"
            >
                📝
            </div>

            @if (
                $studentCount > 0
                && $pretestCompletedCount === $studentCount
            )
                <span
                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    Lengkap
                </span>
            @else
                <span
                    class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                >
                    Dipantau
                </span>
            @endif
        </div>

        <p
            class="mt-4 text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300"
        >
            Pretest
        </p>

        <p
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            {{ $pretestCompletedCount }}
            <span
                class="text-sm text-blue-600 dark:text-blue-300"
            >
                dari {{ $studentCount }}
            </span>
        </p>

        <p
            class="mt-2 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
        >
            Peserta didik telah menyelesaikan asesmen awal.
        </p>
    </div>

    {{-- Penyelesaian Materi Prakelas --}}
    <div
        class="rounded-2xl border border-violet-200 bg-violet-50 p-4 dark:border-violet-900 dark:bg-violet-950/40"
    >
        <div
            class="flex items-start justify-between gap-3"
        >
            <div
                class="flex size-9 items-center justify-center rounded-xl bg-white text-lg shadow-sm dark:bg-neutral-900"
            >
                📖
            </div>

            @if (
                $studentCount > 0
                && $materialCompletedCount === $studentCount
            )
                <span
                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    Lengkap
                </span>
            @else
                <span
                    class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                >
                    Dipantau
                </span>
            @endif
        </div>

        <p
            class="mt-4 text-xs font-semibold uppercase tracking-wide text-violet-600 dark:text-violet-300"
        >
            Materi Prakelas
        </p>

        <p
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            {{ $materialCompletedCount }}
            <span
                class="text-sm text-violet-600 dark:text-violet-300"
            >
                dari {{ $studentCount }}
            </span>
        </p>

        <p
            class="mt-2 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
        >
            Seluruh {{ $requiredMaterialBlockCount }}
            blok materi wajib telah diselesaikan.
        </p>
    </div>

    {{-- Penyelesaian Latihan Mikro --}}
    <div
        class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/40"
    >
        <div
            class="flex items-start justify-between gap-3"
        >
            <div
                class="flex size-9 items-center justify-center rounded-xl bg-white text-lg shadow-sm dark:bg-neutral-900"
            >
                ⚡
            </div>

            @if (
                $studentCount > 0
                && $microExerciseCompletedCount
                    === $studentCount
            )
                <span
                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    Lengkap
                </span>
            @else
                <span
                    class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                >
                    Dipantau
                </span>
            @endif
        </div>

        <p
            class="mt-4 text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300"
        >
            Latihan Mikro
        </p>

        <p
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            {{ $microExerciseCompletedCount }}
            <span
                class="text-sm text-emerald-600 dark:text-emerald-300"
            >
                dari {{ $studentCount }}
            </span>
        </p>

        <p
            class="mt-2 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
        >
            Peserta didik telah menyelesaikan latihan penguatan.
        </p>
    </div>

    {{-- Penyelesaian Kuis Materi --}}
    <div
        class="rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950/40"
    >
        <div
            class="flex items-start justify-between gap-3"
        >
            <div
                class="flex size-9 items-center justify-center rounded-xl bg-white text-lg shadow-sm dark:bg-neutral-900"
            >
                ❓
            </div>

            @if (
                $studentCount > 0
                && $quizCompletedCount === $studentCount
            )
                <span
                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                >
                    Lengkap
                </span>
            @else
                <span
                    class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                >
                    Dipantau
                </span>
            @endif
        </div>

        <p
            class="mt-4 text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-300"
        >
            Kuis Materi
        </p>

        <p
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            {{ $quizCompletedCount }}
            <span
                class="text-sm text-amber-600 dark:text-amber-300"
            >
                dari {{ $studentCount }}
            </span>
        </p>

        <p
            class="mt-2 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
        >
            Peserta didik telah menyelesaikan kuis prakelas.
        </p>
    </div>
</div>
                </div>
            </article>

            {{-- Fokus Guru --}}
            <article class="quest-card p-6 sm:p-7">
                <span class="quest-eyebrow">
                    FOKUS GURU
                </span>

                <h2
                    class="mt-4 text-xl font-bold text-neutral-950 dark:text-white"
                >
                    Prioritas Pembelajaran
                </h2>

                <p
                    class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                >
                    Komponen yang perlu diperiksa sebelum kegiatan tatap muka.
                </p>

                <div class="mt-6 space-y-3">
                    <div
                        class="flex items-start gap-3 rounded-2xl border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-900/60 dark:bg-blue-950/30"
                    >
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-sm font-bold text-blue-600 dark:bg-blue-900 dark:text-blue-300"
                        >
                            1
                        </div>

                        <div>
                            <p
                                class="text-sm font-bold text-neutral-900 dark:text-white"
                            >
                                Periksa penyelesaian pretest
                            </p>

                            <p
                                class="mt-1 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
                            >
                                Pastikan peserta didik telah mengerjakan asesmen
                                awal.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-start gap-3 rounded-2xl border border-violet-100 bg-violet-50/70 p-4 dark:border-violet-900/60 dark:bg-violet-950/30"
                    >
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-sm font-bold text-violet-600 dark:bg-violet-900 dark:text-violet-300"
                        >
                            2
                        </div>

                        <div>
                            <p
                                class="text-sm font-bold text-neutral-900 dark:text-white"
                            >
                                Tinjau aktivitas materi
                            </p>

                            <p
                                class="mt-1 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
                            >
                                Periksa checkpoint membaca dan latihan mikro.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-start gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30"
                    >
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-sm font-bold text-emerald-600 dark:bg-emerald-900 dark:text-emerald-300"
                        >
                            3
                        </div>

                        <div>
                            <p
                                class="text-sm font-bold text-neutral-900 dark:text-white"
                            >
                                Siapkan diskusi masalah
                            </p>

                            <p
                                class="mt-1 text-xs leading-5 text-neutral-500 dark:text-neutral-400"
                            >
                                Gunakan hasil prakelas untuk menentukan fokus
                                diskusi.
                            </p>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        {{-- Alur Blended Learning --}}
        <section
            id="alur-pembelajaran"
            class="quest-card overflow-hidden scroll-mt-6"
        >
            <div
                class="border-b border-neutral-200 px-6 py-6 dark:border-neutral-800"
            >
                <span class="quest-eyebrow">
                    BLENDED LEARNING
                </span>

                <h2
                    class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                >
                    Alur Pembelajaran Questify
                </h2>

                <p
                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Rangkaian kegiatan sebelum, selama, dan setelah
                    pembelajaran tatap muka.
                </p>
            </div>

            <div class="grid gap-5 p-6 lg:grid-cols-3">

                {{-- Prakelas --}}
                <article
                    class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-5 dark:border-blue-900/60 dark:from-blue-950/40 dark:to-neutral-950"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div
                            class="flex size-11 items-center justify-center rounded-2xl bg-blue-600 font-bold text-white shadow-lg shadow-blue-500/20"
                        >
                            01
                        </div>

                        <span
                            class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                        >
                            Sebelum kelas
                        </span>
                    </div>

                    <h3
                        class="mt-5 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Kegiatan Prakelas
                    </h3>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Peserta didik membangun pemahaman awal secara mandiri.
                    </p>

                    <div
                        class="mt-5 space-y-2 text-sm text-neutral-700 dark:text-neutral-300"
                    >
                        <p>• Pretest pilihan ganda</p>
                        <p>• Materi dengan checkpoint baca</p>
                        <p>• Latihan mikro</p>
                        <p>• Kuis per materi</p>
                    </div>
                </article>

                {{-- Tatap Muka --}}
                <article
                    class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-5 dark:border-violet-900/60 dark:from-violet-950/40 dark:to-neutral-950"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div
                            class="flex size-11 items-center justify-center rounded-2xl bg-violet-600 font-bold text-white shadow-lg shadow-violet-500/20"
                        >
                            02
                        </div>

                        <span
                            class="rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-700 dark:bg-violet-950 dark:text-violet-300"
                        >
                            Di kelas
                        </span>
                    </div>

                    <h3
                        class="mt-5 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Kegiatan Tatap Muka
                    </h3>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Guru memfasilitasi pemecahan masalah dan diskusi
                        kolaboratif.
                    </p>

                    <div
                        class="mt-5 space-y-2 text-sm text-neutral-700 dark:text-neutral-300"
                    >
                        <p>• Orientasi masalah PBL</p>
                        <p>• Penyelesaian LKPD</p>
                        <p>• Diskusi dan presentasi</p>
                        <p>• Umpan balik guru</p>
                    </div>
                </article>

                {{-- Pascakelas --}}
                <article
                    class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 dark:border-emerald-900/60 dark:from-emerald-950/40 dark:to-neutral-950"
                >
                    <div class="flex items-center justify-between gap-3">
                        <div
                            class="flex size-11 items-center justify-center rounded-2xl bg-emerald-600 font-bold text-white shadow-lg shadow-emerald-500/20"
                        >
                            03
                        </div>

                        <span
                            class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                        >
                            Setelah kelas
                        </span>
                    </div>

                    <h3
                        class="mt-5 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Kegiatan Pascakelas
                    </h3>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Peserta didik melakukan penguatan dan refleksi hasil
                        belajar.
                    </p>

                    <div
                        class="mt-5 space-y-2 text-sm text-neutral-700 dark:text-neutral-300"
                    >
                        <p>• Refleksi pembelajaran</p>
                        <p>• Tugas penguatan</p>
                        <p>• Final challenge</p>
                        <p>• Rekap perkembangan</p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Menu Utama --}}
        <section>
            <div class="mb-4">
                <h2
                    class="text-xl font-bold text-neutral-950 dark:text-white"
                >
                    Aktivitas Guru
                </h2>

                <p
                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Fitur pengelolaan akan dihubungkan secara bertahap.
                </p>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <article class="quest-card p-5">
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                    >
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5" />
                            <path d="M6 17h12" />
                        </svg>
                    </div>

                    <h3
                        class="mt-5 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Kelola Materi dan Misi
                    </h3>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Mengatur materi, latihan mikro, kuis, misi, dan
                        tantangan pembelajaran.
                    </p>

                    <span
                        class="mt-5 inline-flex rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                    >
                        Segera tersedia
                    </span>
                </article>

                <article class="quest-card p-5">
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-300"
                    >
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21a8 8 0 0 1 16 0" />
                        </svg>
                    </div>

                    <h3
                        class="mt-5 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Monitoring Peserta Didik
                    </h3>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Memeriksa riwayat akses, waktu membaca, percobaan
                        latihan, dan penyelesaian misi.
                    </p>

                    <a
    href="{{ route('teacher.monitoring.index') }}"
    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700"
>
    Buka Monitoring
    <span>→</span>
</a>
                </article>

                <article class="quest-card p-5">
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"
                    >
                        <svg
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 19V9M10 19V5M16 19v-7M22 19H2" />
                        </svg>
                    </div>

                    <h3
                        class="mt-5 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Hasil Pembelajaran
                    </h3>

                    <p
                        class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Melihat hasil pretest, kuis, tantangan, posttest, dan
                        perkembangan belajar peserta didik.
                    </p>

                    <span
                        class="mt-5 inline-flex rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"
                    >
                        Segera tersedia
                    </span>
                </article>
            </div>
        </section>

        {{-- Footer --}}
        <section
            class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400"
        >
            <p>
                Questify — Learn by Doing. Think by Solving.
            </p>

            <span
                class="font-medium text-quest-600 dark:text-quest-300"
            >
                Teacher Workspace
            </span>
        </section>
    </div>
</x-layouts::app>
