<x-layouts::app :title="__('Kelola Soal Posttest')">
    @php
        $questions = $questions ?? collect();

        $totalQuestionCount =
            $totalQuestionCount ?? 0;

        $activeQuestionCount =
            $activeQuestionCount ?? 0;

        $completeQuestionCount =
            $completeQuestionCount ?? 0;

        $totalOptionCount =
            $totalOptionCount ?? 0;

        $optionLabels = [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D',
            5 => 'E',
        ];
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
                            class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-blue-500"
                            ></span>

                            Pengelolaan Posttest
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Soal Posttest
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Kelola pertanyaan, pilihan jawaban,
                            kunci jawaban, urutan, dan status soal
                            pada misi {{ $mission->title }}.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.show',
                            $mission
                        ) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm"
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

        {{-- Ringkasan --}}
        <section
            class="grid gap-4 md:grid-cols-2 xl:grid-cols-4"
        >
            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Total Soal
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950"
                >
                    {{ $totalQuestionCount }}
                </p>

                <p class="mt-2 text-sm text-neutral-500">
                    Seluruh soal Posttest
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Soal Aktif
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950"
                >
                    {{ $activeQuestionCount }}
                </p>

                <p class="mt-2 text-sm text-neutral-500">
                    Dapat dikerjakan peserta didik
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Soal Lengkap
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950"
                >
                    {{ $completeQuestionCount }}
                </p>

                <p class="mt-2 text-sm text-neutral-500">
                    Memiliki satu jawaban benar
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Pilihan Jawaban
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950"
                >
                    {{ $totalOptionCount }}
                </p>

                <p class="mt-2 text-sm text-neutral-500">
                    Total pilihan seluruh soal
                </p>
            </article>
        </section>

        {{-- Daftar soal --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Daftar Pertanyaan
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950"
            >
                Soal yang Tersedia
            </h2>

            <p class="mt-2 text-sm text-neutral-500">
                Jawaban berwarna hijau merupakan kunci jawaban
                yang sedang digunakan.
            </p>

            @if ($questions->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-10 text-center"
                >
                    <p class="font-semibold text-neutral-900">
                        Belum ada soal Posttest
                    </p>
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($questions as $question)
                        <article
                            class="overflow-hidden rounded-2xl border border-neutral-200 bg-white"
                        >
                            <div
                                class="flex flex-col gap-5 border-b border-neutral-200 p-5 lg:flex-row lg:items-start lg:justify-between"
                            >
                                <div class="flex gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 font-bold text-white"
                                    >
                                        {{ $question->sequence }}
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                                        >
                                            Soal Posttest
                                        </p>

                                        <h3
                                            class="mt-2 max-w-4xl text-base font-bold leading-7 text-neutral-950"
                                        >
                                            {{ $question->question_text }}
                                        </h3>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-wrap items-center gap-3"
                                >
                                    @if ($question->is_active)
                                        <span
                                            class="rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700"
                                        >
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600"
                                        >
                                            Nonaktif
                                        </span>
                                    @endif

                                    <a
                                        href="{{ route(
                                            'teacher.missions.posttest.edit',
                                            [
                                                'mission' => $mission,
                                                'question' => $question,
                                            ]
                                        ) }}"
                                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700"
                                    >
                                        Edit Soal
                                        <span>✎</span>
                                    </a>
                                </div>
                            </div>

                            <div
                                class="grid gap-3 p-5 md:grid-cols-2"
                            >
                                @foreach (
                                    $question->options
                                    as $option
                                )
                                    <div
                                        @class([
                                            'rounded-2xl border p-4',
                                            'border-emerald-300 bg-emerald-50'
                                                => $option->is_correct,
                                            'border-neutral-200 bg-neutral-50'
                                                => ! $option->is_correct,
                                        ])
                                    >
                                        <div
                                            class="flex items-start gap-3"
                                        >
                                            <div
                                                @class([
                                                    'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold',
                                                    'bg-emerald-500 text-white'
                                                        => $option->is_correct,
                                                    'bg-white text-neutral-600'
                                                        => ! $option->is_correct,
                                                ])
                                            >
                                                {{ $optionLabels[
                                                    $option->sequence
                                                ] ?? $option->sequence }}
                                            </div>

                                            <div>
                                                <p
                                                    class="text-sm font-semibold text-neutral-900"
                                                >
                                                    {{ $option->option_text }}
                                                </p>

                                                @if ($option->is_correct)
                                                    <p
                                                        class="mt-2 text-xs font-bold text-emerald-600"
                                                    >
                                                        ✓ Jawaban Benar
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
