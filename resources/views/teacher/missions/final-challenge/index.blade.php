<x-layouts::app :title="__('Kelola Final Challenge')">
    @php
        $attemptCount = $attemptCount ?? 0;

        $completedAttemptCount =
            $completedAttemptCount ?? 0;

        $highestScore = $highestScore ?? null;

        $completionPercentage = $attemptCount > 0
            ? round(
                ($completedAttemptCount / $attemptCount) * 100
            )
            : 0;
    @endphp

    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-amber-50 via-white to-orange-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-amber-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-amber-500"
                            ></span>

                            Pengelolaan Final Challenge
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Final Challenge
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Kelola skenario, petunjuk pengerjaan,
                            kode awal, keluaran yang diharapkan,
                            poin maksimal, dan status tantangan
                            akhir pada misi
                            {{ $mission->title }}.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.show',
                            $mission
                        ) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50"
                    >
                        <span>←</span>
                        Kembali ke Detail Misi
                    </a>
                </div>
            </div>
        </section>

        @if (session('success'))
            <section
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700"
            >
                ✓ {{ session('success') }}
            </section>
        @endif

        @if ($challenge === null)
            <section class="quest-card p-10 text-center">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-2xl"
                >
                    🏆
                </div>

                <h2
                    class="mt-5 text-xl font-bold text-neutral-950"
                >
                    Final Challenge Belum Tersedia
                </h2>

                <p
                    class="mx-auto mt-2 max-w-xl text-sm leading-6 text-neutral-500"
                >
                    Misi ini belum mempunyai Final Challenge
                    yang dapat dikelola.
                </p>
            </section>
        @else
            {{-- Ringkasan --}}
            <section
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
            >
                <article class="quest-card p-6">
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                    >
                        Poin Maksimal
                    </p>

                    <p
                        class="mt-3 text-3xl font-bold text-neutral-950"
                    >
                        {{ $challenge->max_points }}
                    </p>

                    <p class="mt-2 text-sm text-neutral-500">
                        Nilai tertinggi tantangan
                    </p>
                </article>

                <article class="quest-card p-6">
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Total Pengerjaan
                    </p>

                    <p
                        class="mt-3 text-3xl font-bold text-neutral-950"
                    >
                        {{ $attemptCount }}
                    </p>

                    <p class="mt-2 text-sm text-neutral-500">
                        Seluruh percobaan peserta didik
                    </p>
                </article>

                <article class="quest-card p-6">
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                    >
                        Pengerjaan Selesai
                    </p>

                    <p
                        class="mt-3 text-3xl font-bold text-neutral-950"
                    >
                        {{ $completedAttemptCount }}
                    </p>

                    <p class="mt-2 text-sm text-neutral-500">
                        {{ $completionPercentage }}%
                        dari seluruh pengerjaan
                    </p>
                </article>

                <article class="quest-card p-6">
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                    >
                        Nilai Tertinggi
                    </p>

                    <p
                        class="mt-3 text-3xl font-bold text-neutral-950"
                    >
                        @if ($highestScore !== null)
                            {{ number_format(
                                (float) $highestScore,
                                0,
                                ',',
                                '.'
                            ) }}
                        @else
                            —
                        @endif
                    </p>

                    <p class="mt-2 text-sm text-neutral-500">
                        Hasil terbaik peserta didik
                    </p>
                </article>
            </section>

            {{-- Informasi utama --}}
            <section class="quest-card overflow-hidden">
                <div
                    class="flex flex-col gap-5 border-b border-neutral-200 p-6 lg:flex-row lg:items-start lg:justify-between"
                >
                    <div class="flex gap-4">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-2xl text-white shadow-sm"
                        >
                            🏆
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                            >
                                Tantangan Akhir
                            </p>

                            <h2
                                class="mt-2 text-xl font-bold text-neutral-950"
                            >
                                {{ $challenge->title }}
                            </h2>

                            <p
                                class="mt-2 text-sm text-neutral-500"
                            >
                                Misi:
                                {{ $mission->title }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-3"
                    >
                        <span
                            class="rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700"
                        >
                            Maksimal
                            {{ $challenge->max_points }}
                            poin
                        </span>

                        @if ($challenge->is_active)
                            <span
                                class="rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-700"
                            >
                                Aktif
                            </span>
                        @else
                            <span
                                class="rounded-full bg-neutral-200 px-4 py-2 text-xs font-semibold text-neutral-600"
                            >
                                Nonaktif
                            </span>
                        @endif

                        <a
                            href="{{ route(
                                'teacher.missions.final-challenge.edit',
                                [
                                    'mission' => $mission,
                                    'finalChallenge' =>
                                        $challenge,
                                ]
                            ) }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600"
                        >
                            Edit Final Challenge
                            <span>✎</span>
                        </a>
                    </div>
                </div>

                <div
                    class="grid gap-5 p-6 lg:grid-cols-2"
                >
                    <article
                        class="rounded-2xl border border-blue-200 bg-blue-50 p-5"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                        >
                            Skenario Tantangan
                        </p>

                        <p
                            class="mt-3 whitespace-pre-line text-sm leading-7 text-neutral-700"
                        >{{ $challenge->scenario }}</p>
                    </article>

                    <article
                        class="rounded-2xl border border-violet-200 bg-violet-50 p-5"
                    >
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                        >
                            Petunjuk Pengerjaan
                        </p>

                        <p
                            class="mt-3 whitespace-pre-line text-sm leading-7 text-neutral-700"
                        >{{ $challenge->instructions }}</p>
                    </article>
                </div>

                <div
                    class="grid gap-5 border-t border-neutral-200 bg-neutral-50 p-6 lg:grid-cols-2"
                >
                    <article>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                        >
                            Kode Awal
                        </p>

                        @if (
                            filled($challenge->starter_code)
                        )
                            <pre
                                class="mt-3 overflow-x-auto whitespace-pre-wrap rounded-2xl bg-neutral-950 p-5 text-sm leading-7 text-neutral-100"
                            ><code>{{ $challenge->starter_code }}</code></pre>
                        @else
                            <p
                                class="mt-3 rounded-2xl border border-dashed border-neutral-300 bg-white p-5 text-sm text-neutral-500"
                            >
                                Kode awal belum tersedia.
                            </p>
                        @endif
                    </article>

                    <article>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-orange-600"
                        >
                            Output yang Diharapkan
                        </p>

                        @if (
                            filled($challenge->expected_output)
                        )
                            <pre
                                class="mt-3 overflow-x-auto whitespace-pre-wrap rounded-2xl border border-orange-200 bg-orange-50 p-5 text-sm leading-7 text-neutral-800"
                            ><code>{{ $challenge->expected_output }}</code></pre>
                        @else
                            <p
                                class="mt-3 rounded-2xl border border-dashed border-neutral-300 bg-white p-5 text-sm text-neutral-500"
                            >
                                Output yang diharapkan belum
                                tersedia.
                            </p>
                        @endif
                    </article>
                </div>
            </section>
        @endif
    </div>
</x-layouts::app>
