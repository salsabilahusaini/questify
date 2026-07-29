<x-layouts::app :title="'Final Challenge - ' . $mission->title">
    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300"
                >
                    <span class="h-2 w-2 rounded-full bg-violet-500"></span>
                    Final Challenge
                </span>

                <h1
                    class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $challenge->title }}
                </h1>

                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                >
                    Terapkan pemahamanmu untuk menganalisis kesalahan,
                    menentukan pola, dan memperbaiki algoritma perulangan.
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
                        class="rounded-full bg-amber-100 px-3 py-2 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                    >
                        Maksimal {{ $challenge->max_points }} poin
                    </span>
                </div>
            </div>
        </section>

        {{-- Informasi tantangan --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Informasi Tantangan
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Selesaikan Permasalahan Berikut
            </h2>

            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                <div
                    class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-white"
                    >
                        Bentuk Jawaban
                    </p>

                    <p
                        class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Kode, output, dan penjelasan
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-white"
                    >
                        Fokus Analisis
                    </p>

                    <p
                        class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        Variabel pengontrol dan kondisi berhenti
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-900 dark:text-white"
                    >
                        Poin Maksimum
                    </p>

                    <p
                        class="mt-1 text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                    >
                        {{ $challenge->max_points }} poin
                    </p>
                </div>
            </div>
        </section>

        {{-- Hasil jika tantangan sudah selesai --}}
        @if ($attempt?->isCompleted())
            <section
                class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 dark:border-emerald-800 dark:bg-emerald-950"
            >
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300"
                >
                    Tantangan Selesai
                </p>

                <h2
                    class="mt-2 text-xl font-bold text-emerald-900 dark:text-emerald-100"
                >
                    Final Challenge Berhasil Diselesaikan
                </h2>

                <p
                    class="mt-2 text-sm leading-6 text-emerald-800 dark:text-emerald-200"
                >
                    Jawabanmu telah disimpan. Berikut ringkasan hasil
                    pengerjaan Final Challenge.
                </p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
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
                            Poin Diperoleh
                        </p>

                        <p
                            class="mt-1 text-2xl font-bold text-emerald-700"
                        >
                            {{ $attempt->earned_points }} poin
                        </p>
                    </div>
                </div>

                @if ($attempt->feedback)
                    <div
                        class="mt-5 rounded-2xl border border-emerald-200 bg-white p-4 dark:border-emerald-800 dark:bg-neutral-900"
                    >
                        <p
                            class="text-sm font-semibold text-neutral-900 dark:text-white"
                        >
                            Umpan Balik
                        </p>

                        <p
                            class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            {{ $attempt->feedback }}
                        </p>
                    </div>
                @endif
            </section>
        @else
            {{-- Skenario masalah --}}
            <section class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Skenario Masalah
                </p>

                <h2
                    class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                >
                    Analisis Permasalahan
                </h2>

                <p
                    class="mt-4 text-sm leading-7 text-neutral-700 dark:text-neutral-200"
                >
                    {{ $challenge->scenario }}
                </p>
            </section>

            {{-- Petunjuk pengerjaan --}}
            <section class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Petunjuk Pengerjaan
                </p>

                <h2
                    class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                >
                    Langkah yang Harus Dilakukan
                </h2>

                <p
                    class="mt-4 text-sm leading-7 text-neutral-700 dark:text-neutral-200"
                >
                    {{ $challenge->instructions }}
                </p>
            </section>

            {{-- Kode awal --}}
            <section class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                >
                    Kode Awal
                </p>

                <h2
                    class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                >
                    Temukan dan Perbaiki Kesalahannya
                </h2>

                <p
                    class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                >
                    Perhatikan hubungan antara nilai awal, kondisi, dan
                    perubahan variabel pengontrol.
                </p>

                <pre
                    class="mt-5 overflow-x-auto rounded-2xl bg-neutral-950 p-5 text-sm leading-7 text-neutral-100"
                ><code>{{ $challenge->starter_code }}</code></pre>
            </section>

            {{-- Kesalahan validasi --}}
@if ($errors->any())
    <section
        class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300"
    >
        <p class="font-semibold">
            Jawaban belum dapat dikirim.
        </p>

        <p class="mt-1">
            {{ $errors->first() }}
        </p>
    </section>
@endif

{{-- Formulir jawaban --}}
<form
    method="POST"
    action="{{ route(
        'student.missions.final-challenge.submit',
        ['mission' => $mission->slug]
    ) }}"
    class="space-y-5"
>
    @csrf

    {{-- Kode perbaikan --}}
    <section class="quest-card p-6">
        <label
            for="answer_code"
            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
        >
            Jawaban Kode
        </label>

        <h2
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            Tuliskan Kode yang Sudah Diperbaiki
        </h2>

        <p
            class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
        >
            Perbaiki bagian kode yang menyebabkan pola keluaran tidak
            sesuai dan perulangan tidak berhenti.
        </p>

        <textarea
            id="answer_code"
            name="answer_code"
            rows="8"
            maxlength="5000"
            required
            spellcheck="false"
            class="mt-5 block w-full rounded-2xl border border-neutral-300 bg-neutral-950 p-5 font-mono text-sm leading-7 text-neutral-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700"
        >{{ old('answer_code', $challenge->starter_code) }}</textarea>

        @error('answer_code')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </section>

    {{-- Output program --}}
    <section class="quest-card p-6">
        <label
            for="answer_output"
            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
        >
            Output Program
        </label>

        <h2
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            Tentukan Keluaran Setelah Kode Diperbaiki
        </h2>

        <p
            class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
        >
            Tuliskan seluruh urutan angka yang dihasilkan program.
        </p>

        <textarea
            id="answer_output"
            name="answer_output"
            rows="3"
            maxlength="1000"
            required
            placeholder="Contoh: 2 4 6 ..."
            class="mt-5 block w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
        >{{ old('answer_output') }}</textarea>

        @error('answer_output')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </section>

    {{-- Penjelasan analisis --}}
    <section class="quest-card p-6">
        <label
            for="answer_explanation"
            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
        >
            Penjelasan Analisis
        </label>

        <h2
            class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
        >
            Jelaskan Kesalahan dan Alasan Perbaikannya
        </h2>

        <p
            class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
        >
            Jelaskan arah perubahan variabel, kondisi berhenti, dan
            hubungan perbaikannya dengan pola bilangan genap.
        </p>

        <textarea
            id="answer_explanation"
            name="answer_explanation"
            rows="7"
            minlength="30"
            maxlength="5000"
            required
            placeholder="Jelaskan hasil analisismu secara lengkap..."
            class="mt-5 block w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-7 text-neutral-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
        >{{ old('answer_explanation') }}</textarea>

        @error('answer_explanation')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
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
                    Jawaban tidak dapat diubah setelah Final Challenge
                    diselesaikan.
                </p>
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700"
            >
                Selesaikan Final Challenge
            </button>
        </div>
    </section>
</form>
        @endif
    </div>
</x-layouts::app>
