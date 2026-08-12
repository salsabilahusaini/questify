<x-layouts::app :title="__('Edit Soal Posttest')">
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
        {{-- Header --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6"
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

                            Pengelolaan Posttest
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950"
                        >
                            Edit Soal Posttest
                        </h1>

                        <p class="mt-2 text-sm text-neutral-600">
                            Perbarui pertanyaan, pilihan jawaban,
                            kunci jawaban, urutan, dan status soal.
                        </p>
                    </div>

                    <a
                        href="{{ route(
    'teacher.missions.posttest.index',
    $mission
) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm"
                    >
                        <span>←</span>
                        Kembali ke Daftar Soal
                    </a>
                </div>
            </div>
        </section>

        {{-- Identitas soal --}}
        <section class="quest-card p-6">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white"
                >
                    {{ $question->sequence }}
                </div>

                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Soal yang Diedit
                    </p>

                    <h2 class="mt-1 font-bold text-neutral-950">
                        Posttest Misi {{ $mission->title }}
                    </h2>

                    <p class="mt-1 text-sm text-neutral-500">
                        {{ $question->options->count() }}
                        pilihan jawaban
                    </p>
                </div>
            </div>
        </section>

        @if (session('error'))
    <div
        class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
    >
        {{ session('error') }}
    </div>
@endif

        {{-- Form --}}
        <section class="quest-card p-6">
            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.posttest.update',
                    [
                        'mission' => $mission,
                        'question' => $question,
                    ]
                ) }}"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

                <div>
                    <label
                        for="question_text"
                        class="text-sm font-semibold text-neutral-800"
                    >
                        Pertanyaan
                    </label>

                    <textarea
                        id="question_text"
                        name="question_text"
                        rows="5"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-6 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                                            class="h-5 w-5 text-emerald-600 focus:ring-emerald-500"
                                        >

                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-sm font-bold text-blue-600 shadow-sm"
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
                                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
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
                                            for="sequence_{{ $option->id }}"
                                            class="text-xs font-semibold uppercase tracking-wide text-neutral-500"
                                        >
                                            Urutan Pilihan
                                        </label>

                                        <input
                                            id="sequence_{{ $option->id }}"
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
                                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                        >
                                        @error(
    'options.'
    . $option->id
    . '.sequence'
)
    <p class="mt-2 text-sm text-red-600">
        {{ $message }}
    </p>
@enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
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
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >

                        @error('sequence')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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
                                class="mt-1 h-5 w-5 rounded text-emerald-600"
                            >

                            <span>
                                <span
                                    class="block font-semibold text-neutral-900"
                                >
                                    Soal Aktif
                                </span>

                                <span
                                    class="mt-1 block text-sm text-neutral-500"
                                >
                                    Soal dapat tampil pada Posttest
                                    peserta didik.
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-neutral-200 pt-6 sm:flex-row sm:justify-end"
                >
                    <a
                        href="{{ route(
                            'teacher.missions.posttest.index',
                            $mission
                        ) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-5 py-3 text-sm font-semibold text-neutral-700"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700"
                    >
                        Simpan Perubahan
                        <span>✓</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts::app>
