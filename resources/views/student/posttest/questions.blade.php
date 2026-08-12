<x-layouts::app :title="'Pengerjaan Posttest - ' . $mission->title">
    <div class="quest-page">
        <section class="quest-hero">
            <div class="relative z-10">
                <div class="quest-eyebrow">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Posttest Sedang Berlangsung
                </div>

                <h1
                    class="mt-5 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $mission->title }}
                </h1>

                <p
                    class="mt-3 max-w-3xl text-neutral-600 dark:text-neutral-300"
                >
                    Jawablah seluruh soal berdasarkan pemahaman yang kamu
                    peroleh setelah menyelesaikan rangkaian pembelajaran.
                    Pastikan semua jawaban telah dipilih sebelum menyelesaikan
                    Posttest.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <span
                        class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300"
                    >
                        {{ $questions->count() }} soal
                    </span>

                    <span
                        class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700 dark:bg-amber-950/50 dark:text-amber-300"
                    >
                        Status: Sedang dikerjakan
                    </span>
                </div>
            </div>
        </section>

        @if ($errors->any())
            <section
                class="rounded-2xl border border-red-200 bg-red-50 p-5 dark:border-red-900 dark:bg-red-950/30"
            >
                <p class="font-semibold text-red-700 dark:text-red-300">
                    Jawaban belum dapat dikirim
                </p>

                <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                    Pastikan seluruh soal telah dijawab dengan pilihan yang
                    valid.
                </p>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('student.missions.posttest.submit', [
                'mission' => $mission->slug,
            ]) }}"
        >
            @csrf

            <div class="space-y-6">
                @foreach ($questions as $question)
                    <section class="quest-card p-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 font-bold text-white"
                            >
                                {{ $loop->iteration }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-sm font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400"
                                >
                                    Soal {{ $loop->iteration }}
                                </p>

                                <h2
                                    class="mt-2 text-lg font-bold leading-7 text-neutral-950 dark:text-white"
                                >
                                    {{ $question->question_text }}
                                </h2>

                                <div class="mt-5 space-y-3">
                                    @foreach ($question->options as $option)
                                        <label
                                            for="posttest-option-{{ $option->id }}"
                                            class="flex cursor-pointer items-start gap-4 rounded-2xl border border-neutral-200 p-4 transition hover:border-emerald-400 hover:bg-emerald-50 dark:border-neutral-700 dark:hover:border-emerald-500 dark:hover:bg-emerald-950/30"
                                        >
                                            <input
                                                id="posttest-option-{{ $option->id }}"
                                                type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $option->id }}"
                                                class="mt-1 size-4 border-neutral-300 text-emerald-600 focus:ring-emerald-500 dark:border-neutral-600 dark:bg-neutral-900"
                                                @checked(
                                                    (string) old(
                                                        'answers.' . $question->id
                                                    ) === (string) $option->id
                                                )
                                            >

                                            <span
                                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-neutral-100 text-sm font-bold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                                            >
                                                {{ chr(64 + $option->sequence) }}
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
                    </section>
                @endforeach
            </div>

            <section class="quest-card mt-6 p-6">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <p
                            class="font-semibold text-neutral-950 dark:text-white"
                        >
                            Periksa kembali jawabanmu
                        </p>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Setelah dikirim, jawaban Posttest tidak dapat
                            diubah.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="quest-button-primary"
                    >
                        Selesaikan Posttest
                    </button>
                </div>
            </section>
        </form>
    </div>
</x-layouts::app>
