<x-layouts::app :title="__('Tambah Soal Latihan Mikro')">
    @php
        $optionLabels = [
            0 => 'A',
            1 => 'B',
            2 => 'C',
            3 => 'D',
        ];

        $selectedBlockId = old(
            'material_block_id',
            $materialBlocks->first()?->id
        );

        $selectedBlockSequence =
            $nextSequenceByBlock->get(
                $selectedBlockId,
                1
            );

        $questionSequence = old(
            'sequence',
            $selectedBlockSequence
        );
    @endphp

    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-emerald-50 via-white to-blue-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-emerald-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-emerald-500"
                            ></span>

                            Pengelolaan Latihan Mikro
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Tambah Soal Latihan Mikro
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Tambahkan soal pilihan ganda pada
                            blok materi dalam misi
                            {{ $mission->title }}.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.micro-exercises.index',
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

        {{-- Form tambah --}}
        <section class="quest-card p-6">
            @if ($materialBlocks->isEmpty())
                <div
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-6"
                >
                    <p class="font-semibold text-amber-800">
                        Belum ada blok materi
                    </p>

                    <p
                        class="mt-2 text-sm leading-6 text-amber-700"
                    >
                        Soal Latihan Mikro belum dapat
                        ditambahkan karena misi ini belum
                        memiliki blok materi.
                    </p>

                    <a
                        href="{{ route(
                            'teacher.missions.show',
                            $mission
                        ) }}"
                        class="mt-4 inline-flex items-center justify-center rounded-xl bg-amber-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-amber-700"
                    >
                        Kembali ke Detail Misi
                    </a>
                </div>
            @else
                <form
                    method="POST"
                    action="{{ route(
                        'teacher.missions.micro-exercises.store',
                        $mission
                    ) }}"
                    class="space-y-6"
                >
                    @csrf

                    {{-- Blok materi --}}
                    <div>
                        <label
                            for="material_block_id"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Blok Materi
                        </label>

                        <select
                            id="material_block_id"
                            name="material_block_id"
                            required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >
                            @foreach (
                                $materialBlocks
                                as $materialBlock
                            )
                                <option
                                    value="{{ $materialBlock->id }}"
                                    data-next-sequence="{{ $nextSequenceByBlock->get(
                                        $materialBlock->id,
                                        1
                                    ) }}"
                                    @selected(
                                        (int) $selectedBlockId
                                        === (int) $materialBlock->id
                                    )
                                >
                                    {{ $materialBlock
                                        ->materialSection
                                        ?->title
                                        ?? 'Bagian Materi' }}
                                    · Blok
                                    {{ $materialBlock->sequence }}
                                    ·
                                    {{ $materialBlock->title
                                        ?: ucfirst(
                                            $materialBlock->type
                                        ) }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-neutral-500">
                            Soal akan ditampilkan setelah
                            peserta didik menyelesaikan blok
                            materi yang dipilih.
                        </p>

                        @error('material_block_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pertanyaan --}}
                    <div>
                        <label
                            for="question_text"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Pertanyaan Latihan Mikro
                        </label>

                        <textarea
                            id="question_text"
                            name="question_text"
                            rows="5"
                            required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >{{ old('question_text') }}</textarea>

                        @error('question_text')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pilihan jawaban --}}
                    <div>
                        <p
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Pilihan Jawaban dan Kunci Jawaban
                        </p>

                        <p class="mt-2 text-xs text-neutral-500">
                            Isi empat pilihan jawaban, kemudian
                            pilih tepat satu lingkaran sebagai
                            jawaban yang benar.
                        </p>

                        @error('correct_option_index')
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
                                $optionLabels
                                as $optionIndex => $optionLabel
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
                                                name="correct_option_index"
                                                value="{{ $optionIndex }}"
                                                @checked(
                                                    (string) old(
                                                        'correct_option_index'
                                                    )
                                                    === (string) $optionIndex
                                                )
                                                required
                                                class="h-5 w-5 text-emerald-600 focus:ring-emerald-500"
                                            >

                                            <span
                                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-sm font-bold text-emerald-600 shadow-sm"
                                            >
                                                {{ $optionLabel }}
                                            </span>
                                        </label>

                                        <div>
                                            <label
                                                for="option_{{ $optionIndex }}"
                                                class="text-xs font-semibold uppercase tracking-wide text-neutral-500"
                                            >
                                                Teks Pilihan
                                                {{ $optionLabel }}
                                            </label>

                                            <input
                                                id="option_{{ $optionIndex }}"
                                                name="options[{{ $optionIndex }}][option_text]"
                                                type="text"
                                                value="{{ old(
                                                    'options.'
                                                        . $optionIndex
                                                        . '.option_text'
                                                ) }}"
                                                required
                                                class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                                            >

                                            @error(
                                                'options.'
                                                . $optionIndex
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
                                                for="option_sequence_{{ $optionIndex }}"
                                                class="text-xs font-semibold uppercase tracking-wide text-neutral-500"
                                            >
                                                Urutan Pilihan
                                            </label>

                                            <input
                                                id="option_sequence_{{ $optionIndex }}"
                                                name="options[{{ $optionIndex }}][sequence]"
                                                type="number"
                                                min="1"
                                                value="{{ old(
                                                    'options.'
                                                        . $optionIndex
                                                        . '.sequence',
                                                    $optionIndex + 1
                                                ) }}"
                                                required
                                                class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                                            >

                                            @error(
                                                'options.'
                                                . $optionIndex
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

                    {{-- Poin dan urutan soal --}}
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label
                                for="points"
                                class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                            >
                                Poin Soal
                            </label>

                            <input
                                id="points"
                                name="points"
                                type="number"
                                min="0"
                                max="10000"
                                value="{{ old('points', 10) }}"
                                required
                                class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                            >

                            <p
                                class="mt-2 text-xs text-neutral-500"
                            >
                                Nilai awal poin adalah 10 dan
                                dapat disesuaikan.
                            </p>

                            @error('points')
                                <p
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="sequence"
                                class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                            >
                                Urutan Soal
                            </label>

                            <input
                                id="sequence"
                                name="sequence"
                                type="number"
                                min="1"
                                value="{{ $questionSequence }}"
                                required
                                class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                            >

                            <p
                                class="mt-2 text-xs text-neutral-500"
                            >
                                Urutan berikutnya akan
                                disesuaikan dengan blok materi
                                yang dipilih.
                            </p>

                            @error('sequence')
                                <p
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Umpan balik --}}
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label
                                for="correct_feedback"
                                class="text-sm font-semibold text-emerald-700"
                            >
                                Umpan Balik Jawaban Benar
                            </label>

                            <textarea
                                id="correct_feedback"
                                name="correct_feedback"
                                rows="5"
                                class="mt-2 w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                            >{{ old('correct_feedback') }}</textarea>

                            @error('correct_feedback')
                                <p
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="incorrect_feedback"
                                class="text-sm font-semibold text-red-700"
                            >
                                Umpan Balik Jawaban Salah
                            </label>

                            <textarea
                                id="incorrect_feedback"
                                name="incorrect_feedback"
                                rows="5"
                                class="mt-2 w-full rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-red-500 focus:ring-4 focus:ring-red-100"
                            >{{ old('incorrect_feedback') }}</textarea>

                            @error('incorrect_feedback')
                                <p
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
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
                                @checked(old('is_active', true))
                                class="mt-1 h-5 w-5 rounded text-emerald-600 focus:ring-emerald-500"
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
                                    Jika dicentang, soal dapat
                                    tampil pada Latihan Mikro
                                    peserta didik.
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
                                'teacher.missions.micro-exercises.index',
                                $mission
                            ) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-5 py-3 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                        >
                            Tambahkan Soal
                            <span>+</span>
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </div>

    @if ($materialBlocks->isNotEmpty())
        <script>
            document.addEventListener(
                'DOMContentLoaded',
                function () {
                    const blockSelect =
                        document.getElementById(
                            'material_block_id'
                        );

                    const sequenceInput =
                        document.getElementById(
                            'sequence'
                        );

                    if (
                        blockSelect === null
                        || sequenceInput === null
                    ) {
                        return;
                    }

                    blockSelect.addEventListener(
                        'change',
                        function () {
                            const selectedOption =
                                blockSelect.options[
                                    blockSelect.selectedIndex
                                ];

                            sequenceInput.value =
                                selectedOption.dataset
                                    .nextSequence ?? 1;
                        }
                    );
                }
            );
        </script>
    @endif
</x-layouts::app>
