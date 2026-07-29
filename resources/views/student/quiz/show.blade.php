<x-layouts::app :title="'Kuis Materi - ' . $mission->title">
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
                    Kuis Materi
                </span>

                <h1
                    class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                >
                    Kuis Materi: {{ $mission->title }}
                </h1>

                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                >
                    Uji pemahamanmu setelah menyelesaikan Materi Prakelas
                    dan Latihan Mikro.
                </p>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <a
                        href="{{ route(
                            'student.missions.show',
                            ['mission' => $mission->slug]
                        ) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Kembali ke Detail Misi
                    </a>

                    <span
                        class="rounded-full bg-violet-100 px-3 py-2 text-xs font-semibold text-violet-700 dark:bg-violet-950 dark:text-violet-300"
                    >
                        {{ $questions->count() }} soal tersedia
                    </span>
                </div>
            </div>
        </section>

        {{-- Informasi kuis --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Informasi Kuis
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Persiapan Sebelum Mengerjakan
            </h2>

            <p
                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
            >
                Bacalah setiap pertanyaan dengan teliti. Setiap soal
                memiliki satu jawaban yang paling tepat.
            </p>

            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                <div
                    class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-white"
                    >
                        Jumlah Soal
                    </p>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ $questions->count() }} soal pilihan ganda
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-white"
                    >
                        Ketentuan
                    </p>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Jawab seluruh soal yang tersedia
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-white"
                    >
                        Total Poin
                    </p>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ $questions->sum('points') }} poin
                    </p>
                </div>
            </div>
        </section>

        {{-- Ringkasan jika kuis sudah selesai --}}
        @if ($attempt?->isCompleted())
            <section
                class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 dark:border-emerald-800 dark:bg-emerald-950"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300"
                >
                    Kuis Selesai
                </p>

                <h2
                    class="mt-2 text-xl font-bold text-emerald-900 dark:text-emerald-100"
                >
                    Kuis Materi Berhasil Diselesaikan
                </h2>

                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    <div
                        class="rounded-2xl border border-emerald-200 bg-white p-4 dark:border-emerald-800 dark:bg-neutral-900"
                    >
                        <p class="text-sm text-neutral-500">
                            Nilai
                        </p>

                        <p
                            class="mt-1 text-2xl font-bold text-emerald-700"
                        >
                            {{ number_format((float) $attempt->score, 2) }}
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-emerald-200 bg-white p-4 dark:border-emerald-800 dark:bg-neutral-900"
                    >
                        <p class="text-sm text-neutral-500">
                            Jawaban Benar
                        </p>

                        <p
                            class="mt-1 text-2xl font-bold text-emerald-700"
                        >
                            {{ $attempt->correct_answers }}
                            dari
                            {{ $attempt->total_questions }}
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-emerald-200 bg-white p-4 dark:border-emerald-800 dark:bg-neutral-900"
                    >
                        <p class="text-sm text-neutral-500">
                            Poin Diperoleh
                        </p>

                        <p
                            class="mt-1 text-2xl font-bold text-emerald-700"
                        >
                            {{ $attempt->earned_points }} poin
                        </p>
                    </div>
                </div>
            </section>
        @else
    {{-- Pesan kesalahan validasi --}}
    @if ($errors->any())
        <section
            class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300"
        >
            <p class="font-semibold">
                Kuis belum dapat dikirim.
            </p>

            <p class="mt-1">
                {{ $errors->first() }}
            </p>
        </section>
    @endif

    <form
        method="POST"
        action="{{ route(
            'student.missions.quiz.submit',
            ['mission' => $mission->slug]
        ) }}"
        class="space-y-5"
    >
        @csrf

        {{-- Daftar soal --}}
        <section class="space-y-5">
            @foreach ($questions as $questionIndex => $question)
                <article class="quest-card p-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-sm font-bold text-white"
                        >
                            {{ $questionIndex + 1 }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div
                                class="flex flex-wrap items-center gap-2"
                            >
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                                >
                                    Soal {{ $questionIndex + 1 }}
                                </p>

                                <span
                                    class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700"
                                >
                                    {{ $question->points }} poin
                                </span>
                            </div>

                            <h2
                                class="mt-3 text-base font-bold leading-7 text-neutral-950 dark:text-white"
                            >
                                {{ $question->question_text }}
                            </h2>

                            <div class="mt-5 space-y-3">
                                @foreach ($question->options as $optionIndex => $option)
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-neutral-200 px-4 py-3 transition hover:border-blue-300 hover:bg-blue-50 dark:border-neutral-700 dark:hover:border-blue-600 dark:hover:bg-neutral-800"
                                    >
                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $option->id }}"
                                            class="mt-2 h-4 w-4 shrink-0 border-neutral-300 text-blue-600 focus:ring-blue-500"
                                            @checked(
                                                (string) old(
                                                    'answers.' . $question->id
                                                ) === (string) $option->id
                                            )
                                            required
                                        >

                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-xs font-bold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                                        >
                                            {{ chr(65 + $optionIndex) }}
                                        </span>

                                        <span
                                            class="pt-1 text-sm leading-6 text-neutral-700 dark:text-neutral-200"
                                        >
                                            {{ $option->option_text }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        {{-- Tombol pengiriman --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2
                        class="font-bold text-neutral-950 dark:text-white"
                    >
                        Periksa kembali jawabanmu
                    </h2>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Jawaban tidak dapat diubah setelah Kuis Materi
                        diselesaikan.
                    </p>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    Selesaikan Kuis Materi
                </button>
            </div>
        </section>
    </form>
@endif
    </div>
</x-layouts::app>
