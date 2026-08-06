<x-layouts::app :title="'Latihan Mikro - ' . $mission->title">
    <div class="quest-page">
        <section class="space-y-5">
            {{-- Header halaman --}}
            <div class="quest-card overflow-hidden">
                <div
                    class="bg-gradient-to-r from-blue-50 via-white to-violet-100 px-6 py-7"
                >
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700"
                    >
                        <span
                            class="h-2 w-2 rounded-full bg-violet-500"
                        ></span>

                        Latihan Mikro
                    </div>

                    <h1
                        class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                    >
                        Latihan Mikro:
                        {{ $mission->title }}
                    </h1>

                    <p
                        class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                    >
                        Selesaikan latihan singkat untuk menguji pemahamanmu
                        setelah mempelajari Materi Prakelas.
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
                            class="inline-flex rounded-full bg-violet-100 px-3 py-2 text-xs font-semibold text-violet-700"
                        >
                            {{ $questions->count() }} soal tersedia
                        </span>
                    </div>
                </div>
            </div>

            {{-- Informasi pengerjaan --}}
            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Informasi Latihan
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
                    memiliki satu jawaban yang paling tepat dan umpan balik
                    akan diberikan setelah latihan dikirim.
                </p>

                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div
                        class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                    >
                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">
                            Jumlah Soal
                        </p>

                        <p class="mt-1 text-sm text-neutral-500">
                            {{ $questions->count() }} soal pilihan ganda
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                    >
                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">
                            Kesempatan
                        </p>

                        <p class="mt-1 text-sm text-neutral-500">
                            Kerjakan sesuai pemahamanmu
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                    >
                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">
                            Total Poin
                        </p>

                        <p class="mt-1 text-sm text-neutral-500">
                            {{ $questions->sum('points') }} poin
                        </p>
                    </div>
                </div>
            </div>

            {{-- Hasil pengerjaan --}}
@if ($attempt?->isCompleted())
    <div
        class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6"
    >
        <p
            class="text-xs font-semibold uppercase tracking-wider text-emerald-700"
        >
            Latihan Selesai
        </p>

        <h2 class="mt-2 text-xl font-bold text-emerald-900">
            Latihan Mikro Berhasil Diselesaikan
        </h2>

        <p class="mt-2 text-sm leading-6 text-emerald-800">
            Jawabanmu telah tersimpan. Berikut ringkasan hasil
            pengerjaan Latihan Mikro.
        </p>

        <div class="mt-5 grid gap-4 sm:grid-cols-3">
            <div
                class="rounded-2xl border border-emerald-200 bg-white p-4"
            >
                <p class="text-sm font-medium text-neutral-500">
                    Nilai
                </p>

                <p class="mt-1 text-2xl font-bold text-emerald-700">
                    {{ number_format((float) $attempt->score, 2) }}
                </p>
            </div>

            <div
                class="rounded-2xl border border-emerald-200 bg-white p-4"
            >
                <p class="text-sm font-medium text-neutral-500">
                    Jawaban Benar
                </p>

                <p class="mt-1 text-2xl font-bold text-emerald-700">
                    {{ $attempt->correct_answers }}
                    dari
                    {{ $attempt->total_questions }}
                </p>
            </div>

            <div
                class="rounded-2xl border border-emerald-200 bg-white p-4"
            >
                <p class="text-sm font-medium text-neutral-500">
                    Poin Diperoleh
                </p>

                <p class="mt-1 text-2xl font-bold text-emerald-700">
                    {{ $attempt->earned_points }} poin
                </p>
            </div>
        </div>
                {{-- Umpan balik setiap jawaban --}}
        <div class="mt-6 space-y-4">
            <h3 class="font-bold text-emerald-900">
                Umpan Balik Jawaban
            </h3>

            @foreach ($attempt->answers as $answerIndex => $answer)
                <article
                    class="rounded-2xl border p-4
                        {{ $answer->is_correct
                            ? 'border-emerald-200 bg-white'
                            : 'border-amber-200 bg-amber-50' }}"
                >
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider
                                    {{ $answer->is_correct
                                        ? 'text-emerald-700'
                                        : 'text-amber-700' }}"
                            >
                                Soal {{ $answerIndex + 1 }}
                            </p>

                            <p class="mt-2 font-semibold text-neutral-900">
                                {{ $answer->question?->question_text }}
                            </p>

                            <p class="mt-2 text-sm text-neutral-600">
                                Jawabanmu:
                                <span class="font-semibold">
                                    {{ $answer->selectedOption?->option_text }}
                                </span>
                            </p>
                        </div>

                        <span
                            class="w-fit rounded-full px-3 py-1.5 text-xs font-semibold
                                {{ $answer->is_correct
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-amber-100 text-amber-700' }}"
                        >
                            {{ $answer->is_correct
                                ? 'Jawaban Benar'
                                : 'Jawaban Belum Tepat' }}
                        </span>
                    </div>

                    <div
                        class="mt-4 rounded-xl border border-neutral-200 bg-neutral-50 p-4"
                    >
                        <p class="text-sm leading-6 text-neutral-700">
                            {{ $answer->is_correct
                                ? $answer->question?->correct_feedback
                                : $answer->question?->incorrect_feedback }}
                        </p>
                    </div>

                    <p class="mt-3 text-xs font-semibold text-neutral-500">
                        Poin diperoleh:
                        {{ $answer->awarded_points }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
@else
    <form
        method="POST"
        action="{{ route(
            'student.missions.micro-exercise.submit',
            ['mission' => $mission->slug]
        ) }}"
        class="space-y-5"
    >
        @csrf

        @if ($errors->any())
            <div
                class="rounded-2xl border border-red-200 bg-red-50 p-5"
            >
                <p class="font-semibold text-red-800">
                    Jawaban belum dapat dikirim
                </p>

                <p class="mt-1 text-sm text-red-700">
                    Pastikan seluruh soal sudah dijawab dengan pilihan
                    yang tersedia.
                </p>
            </div>
        @endif

        {{-- Daftar soal --}}
        @foreach ($questions as $questionIndex => $question)
            <article class="quest-card p-6">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-sm font-bold text-white"
                    >
                        {{ $questionIndex + 1 }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
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

                        @if (filled($question->materialBlock?->title))
                            <p
                                class="mt-2 text-xs font-medium text-neutral-500"
                            >
                                Berkaitan dengan:
                                {{ $question->materialBlock->title }}
                            </p>
                        @endif

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
                                        class="mt-1 h-4 w-4 shrink-0 border-neutral-300 text-blue-600 focus:ring-blue-500"
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

        {{-- Tombol pengiriman --}}
        <div class="quest-card p-6">
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
                        Jawaban tidak dapat diubah setelah Latihan Mikro
                        diselesaikan.
                    </p>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    Selesaikan Latihan Mikro
                </button>
            </div>
        </div>
    </form>
@endif
        </section>
    </div>
</x-layouts::app>
