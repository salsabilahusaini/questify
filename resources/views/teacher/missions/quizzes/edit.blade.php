<x-layouts::app :title="__('Edit Soal Kuis Materi')">
    @php
        $correctOptionId = old(
            'correct_option_id',
            $question->options
                ->firstWhere('is_correct', true)?->id
        );

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
                            Edit Soal Kuis Materi
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Perbarui pertanyaan, pilihan jawaban,
                            kunci jawaban, poin, penjelasan,
                            urutan, dan status soal.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.quizzes.index',
                            $mission
                        ) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50"
                    >
                        <span>←</span>
                        Kembali ke Daftar Soal
                    </a>
                </div>
            </div>
        </section>

        {{-- Informasi soal --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-blue-600 text-lg font-bold text-white shadow-sm"
                    >
                        {{ $question->sequence }}
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                        >
                            Soal yang Diedit
                        </p>

                        <h2
                            class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                        >
                            Kuis Materi
                        </h2>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Misi: {{ $mission->title }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <span
                        class="rounded-full bg-blue-100 px-4 py-2 text-xs font-semibold text-blue-700"
                    >
                        {{ $question->options->count() }}
                        pilihan
                    </span>

                    <span
                        class="rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold text-amber-700"
                    >
                        +{{ $question->points }} poin
                    </span>
                </div>
            </div>
        </section>

        {{-- Form edit --}}
        <section class="quest-card p-6">
            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.quizzes.update',
                    [
                        'mission' => $mission,
                        'question' => $question,
                    ]
                ) }}"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- Pertanyaan --}}
                <div>
                    <label
                        for="question_text"
                        class="text-sm font-semibold text-neutral-800"
                    >
                        Pertanyaan Kuis Materi
                    </label>

                    <textarea
                        id="question_text"
                        name="question_text"
                        rows="5"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                    >{{ old(
                        'question_text',
                        $question->question_text
                    ) }}</textarea>

                    @error('question_text')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Pilihan jawaban --}}
                <div>
                    <p
                        class="text-sm font-semibold text-neutral-800"
                    >
                        Pilihan Jawaban dan Kunci Jawaban
                    </p>

                    <p class="mt-2 text-xs text-neutral-500">
                        Pilih tepat satu lingkaran sebagai jawaban
                        yang benar.
                    </p>

                    @error('correct_option_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('options')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="mt-4 space-y-4">
                        @foreach (
                            $question->options
                            as $option
                        )
                            <div
                                class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5"
                            >
                                <div
                                    class="grid gap-4 lg:grid-cols-[auto_1fr_160px]"
                                >
                                    <label
                                        class="flex cursor-pointer items-center gap-3"
                                    >
                                        <input
                                            type="radio"
                                            name="correct_option_id"
                                            value="{{ $option->id }}"
                                            @checked(
                                                (int) $correctOptionId
                                                === (int) $option->id
                                            )
                                            required
                                            class="h-5 w-5 text-violet-600 focus:ring-violet-500"
                                        >

                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-sm font-bold text-violet-600 shadow-sm"
                                        >
                                            {{ $optionLabels[
                                                $option->sequence
                                            ] ?? $option->sequence }}
                                        </span>
                                    </label>

                                    <div>
                                        <label
                                            for="option_{{ $option->id }}"
                                            class="text-xs font-semibold uppercase tracking-wide text-neutral-500"
                                        >
                                            Teks Pilihan
                                        </label>

                                        <input
                                            id="option_{{ $option->id }}"
                                            name="options[{{ $option->id }}][option_text]"
                                            type="text"
                                            value="{{ old(
                                                'options.'
                                                    . $option->id
                                                    . '.option_text',
                                                $option->option_text
                                            ) }}"
                                            required
                                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                                        >

                                        @error(
                                            'options.'
                                            . $option->id
                                            . '.option_text'
                                        )
                                            <p
                                                class="mt-2 text-sm text-red-600"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            for="option_sequence_{{ $option->id }}"
                                            class="text-xs font-semibold uppercase tracking-wide text-neutral-500"
                                        >
                                            Urutan Pilihan
                                        </label>

                                        <input
                                            id="option_sequence_{{ $option->id }}"
                                            name="options[{{ $option->id }}][sequence]"
                                            type="number"
                                            min="1"
                                            value="{{ old(
                                                'options.'
                                                    . $option->id
                                                    . '.sequence',
                                                $option->sequence
                                            ) }}"
                                            required
                                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                                        >

                                        @error(
                                            'options.'
                                            . $option->id
                                            . '.sequence'
                                        )
                                            <p
                                                class="mt-2 text-sm text-red-600"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Poin dan urutan --}}
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label
                            for="points"
                            class="text-sm font-semibold text-neutral-800"
                        >
                            Poin Soal
                        </label>

                        <input
                            id="points"
                            name="points"
                            type="number"
                            min="0"
                            max="10000"
                            value="{{ old(
                                'points',
                                $question->points
                            ) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                        >

                        <p class="mt-2 text-xs text-neutral-500">
                            Poin diperoleh jika peserta didik
                            menjawab soal dengan benar.
                        </p>

                        @error('points')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="sequence"
                            class="text-sm font-semibold text-neutral-800"
                        >
                            Urutan Soal
                        </label>

                        <input
                            id="sequence"
                            name="sequence"
                            type="number"
                            min="1"
                            value="{{ old(
                                'sequence',
                                $question->sequence
                            ) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                        >

                        <p class="mt-2 text-xs text-neutral-500">
                            Urutan menentukan posisi soal pada
                            Kuis Materi peserta didik.
                        </p>

                        @error('sequence')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Penjelasan --}}
                <div>
                    <label
                        for="explanation"
                        class="text-sm font-semibold text-violet-700"
                    >
                        Penjelasan Jawaban
                    </label>

                    <textarea
                        id="explanation"
                        name="explanation"
                        rows="6"
                        class="mt-2 w-full rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                    >{{ old(
                        'explanation',
                        $question->explanation
                    ) }}</textarea>

                    <p class="mt-2 text-xs text-neutral-500">
                        Penjelasan membantu peserta didik
                        memahami alasan jawaban yang benar.
                    </p>

                    @error('explanation')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status aktif --}}
                <div
                    class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5"
                >
                    <label
                        for="is_active"
                        class="flex cursor-pointer items-start gap-4"
                    >
                        <input
                            id="is_active"
                            name="is_active"
                            type="checkbox"
                            value="1"
                            @checked(
                                old(
                                    'is_active',
                                    $question->is_active
                                )
                            )
                            class="mt-1 h-5 w-5 rounded text-violet-600 focus:ring-violet-500"
                        >

                        <span>
                            <span
                                class="block font-semibold text-neutral-900"
                            >
                                Soal Aktif
                            </span>

                            <span
                                class="mt-1 block text-sm leading-6 text-neutral-500"
                            >
                                Jika dicentang, soal dapat tampil
                                pada Kuis Materi peserta didik.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Tombol --}}
                <div
                    class="flex flex-col-reverse gap-3 border-t border-neutral-200 pt-6 sm:flex-row sm:justify-end"
                >
                    <a
                        href="{{ route(
                            'teacher.missions.quizzes.index',
                            $mission
                        ) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-5 py-3 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-violet-700"
                    >
                        Simpan Perubahan
                        <span>✓</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts::app>
