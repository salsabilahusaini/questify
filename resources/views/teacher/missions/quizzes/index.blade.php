<x-layouts::app :title="__('Kelola Kuis Materi')">
    @php
        $questions = $questions ?? collect();

        $totalQuestionCount =
            $totalQuestionCount ?? 0;

        $activeQuestionCount =
            $activeQuestionCount ?? 0;

        $completeQuestionCount =
            $completeQuestionCount ?? 0;

        $totalPointCount =
            $totalPointCount ?? 0;

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
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-violet-50 via-white to-blue-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-violet-500"
                            ></span>

                            Pengelolaan Kuis Materi
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Soal Kuis Materi
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Kelola pertanyaan, pilihan jawaban,
                            kunci jawaban, poin, penjelasan,
                            urutan, dan status Kuis Materi pada
                            misi {{ $mission->title }}.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
    <a
        href="{{ route(
            'teacher.missions.quizzes.create',
            $mission
        ) }}"
        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-violet-700"
    >
        <span>＋</span>
        Tambah Soal
    </a>

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
            </div>
        </section>

        @if (session('success'))
            <section
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700"
            >
                ✓ {{ session('success') }}
            </section>
        @endif

        @if (session('error'))
    <section
        class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700"
    >
        ! {{ session('error') }}
    </section>
@endif

        {{-- Ringkasan --}}
        <section
            class="grid gap-4 md:grid-cols-2 xl:grid-cols-5"
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
                    Seluruh soal Kuis Materi
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
                    Total Poin
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950"
                >
                    {{ $totalPointCount }}
                </p>

                <p class="mt-2 text-sm text-neutral-500">
                    Poin seluruh soal
                </p>
            </article>

            <article class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-cyan-600"
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
                Kuis Materi yang Tersedia
            </h2>

            <p class="mt-2 text-sm text-neutral-500">
                Pilihan berwarna hijau merupakan kunci jawaban.
                Penjelasan soal ditampilkan setelah pilihan
                jawaban.
            </p>

            @if ($questions->isEmpty())
                <div
                    class="mt-6 rounded-2xl border border-dashed border-neutral-300 p-10 text-center"
                >
                    <p class="font-semibold text-neutral-900">
                        Belum ada soal Kuis Materi
                    </p>

                    <p class="mt-2 text-sm text-neutral-500">
                        Soal Kuis Materi belum tersedia pada
                        misi ini.
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
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-blue-600 font-bold text-white"
                                    >
                                        {{ $question->sequence }}
                                    </div>

                                    <div>
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                                        >
                                            Soal Kuis Materi
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
                                    <span
                                        class="rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700"
                                    >
                                        +{{ $question->points }} poin
                                    </span>

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
                                            'teacher.missions.quizzes.edit',
                                            [
                                                'mission' => $mission,
                                                'question' => $question,
                                            ]
                                        ) }}"
                                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"
                                    >
                                        Edit Soal

                                        <form
    method="POST"
    action="{{ route(
        'teacher.missions.quizzes.destroy',
        [
            'mission' => $mission,
            'question' => $question,
        ]
    ) }}"
    onsubmit="return confirm(
        'Yakin ingin menghapus soal Kuis Materi ini? Soal yang sudah memiliki jawaban peserta didik tidak dapat dihapus.'
    );"
>
    @csrf
    @method('DELETE')

    <button
        type="submit"
        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-red-700"
    >
        Hapus Soal
        <span>⌫</span>
    </button>
</form>
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

                            <div
                                class="border-t border-neutral-200 bg-violet-50/50 p-5"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                                >
                                    Penjelasan Jawaban
                                </p>

                                <p
                                    class="mt-2 text-sm leading-6 text-neutral-600"
                                >
                                    {{ $question->explanation
                                        ?: 'Belum ada penjelasan jawaban.' }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
