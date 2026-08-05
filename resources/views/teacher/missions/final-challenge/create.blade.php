<x-layouts::app :title="__('Tambah Final Challenge')">
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
                            Tambah Final Challenge
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Tambahkan skenario, petunjuk
                            pengerjaan, kode awal, output yang
                            diharapkan, serta poin maksimal untuk
                            misi {{ $mission->title }}.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.final-challenge.index',
                            $mission
                        ) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50"
                    >
                        <span>←</span>
                        Kembali ke Final Challenge
                    </a>
                </div>
            </div>
        </section>

        {{-- Informasi misi --}}
        <section class="quest-card p-6">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-2xl text-white shadow-sm"
                >
                    🏆
                </div>

                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                    >
                        Tantangan Akhir Baru
                    </p>

                    <h2
                        class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Final Challenge
                    </h2>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Misi: {{ $mission->title }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Form tambah --}}
        <section class="quest-card p-6">
            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.final-challenge.store',
                    $mission
                ) }}"
                class="space-y-6"
            >
                @csrf

                {{-- Judul --}}
                <div>
                    <label
                        for="title"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Judul Final Challenge
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title') }}"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                    >

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Skenario --}}
                <div>
                    <label
                        for="scenario"
                        class="text-sm font-semibold text-blue-700"
                    >
                        Skenario Tantangan
                    </label>

                    <textarea
                        id="scenario"
                        name="scenario"
                        rows="7"
                        required
                        class="mt-2 w-full rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm leading-7 text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    >{{ old('scenario') }}</textarea>

                    <p class="mt-2 text-xs text-neutral-500">
                        Jelaskan masalah atau situasi yang harus
                        diselesaikan oleh peserta didik.
                    </p>

                    @error('scenario')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Petunjuk --}}
                <div>
                    <label
                        for="instructions"
                        class="text-sm font-semibold text-violet-700"
                    >
                        Petunjuk Pengerjaan
                    </label>

                    <textarea
                        id="instructions"
                        name="instructions"
                        rows="7"
                        required
                        class="mt-2 w-full rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm leading-7 text-neutral-900 outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100"
                    >{{ old('instructions') }}</textarea>

                    <p class="mt-2 text-xs text-neutral-500">
                        Tuliskan langkah atau ketentuan yang harus
                        dilakukan peserta didik.
                    </p>

                    @error('instructions')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Kode awal dan output --}}
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label
                            for="starter_code"
                            class="text-sm font-semibold text-emerald-700"
                        >
                            Kode Awal
                        </label>

                        <textarea
                            id="starter_code"
                            name="starter_code"
                            rows="12"
                            spellcheck="false"
                            class="mt-2 w-full rounded-xl border border-neutral-700 bg-neutral-950 px-4 py-3 font-mono text-sm leading-7 text-neutral-100 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                        >{{ old('starter_code') }}</textarea>

                        <p class="mt-2 text-xs text-neutral-500">
                            Kode yang menjadi titik awal pengerjaan
                            peserta didik. Bagian ini boleh
                            dikosongkan.
                        </p>

                        @error('starter_code')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="expected_output"
                            class="text-sm font-semibold text-orange-700"
                        >
                            Output yang Diharapkan
                        </label>

                        <textarea
                            id="expected_output"
                            name="expected_output"
                            rows="12"
                            spellcheck="false"
                            class="mt-2 w-full rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 font-mono text-sm leading-7 text-neutral-900 outline-none transition focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                        >{{ old('expected_output') }}</textarea>

                        <p class="mt-2 text-xs text-neutral-500">
                            Output yang seharusnya dihasilkan
                            setelah kode diselesaikan. Bagian ini
                            boleh dikosongkan.
                        </p>

                        @error('expected_output')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Poin maksimal --}}
                <div>
                    <label
                        for="max_points"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Poin Maksimal
                    </label>

                    <input
                        id="max_points"
                        name="max_points"
                        type="number"
                        min="0"
                        max="10000"
                        value="{{ old('max_points', 100) }}"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
                    >

                    <p class="mt-2 text-xs text-neutral-500">
                        Nilai awal poin maksimal adalah 100 dan
                        dapat disesuaikan.
                    </p>

                    @error('max_points')
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
                            @checked(old('is_active', true))
                            class="mt-1 h-5 w-5 rounded text-amber-600 focus:ring-amber-500"
                        >

                        <span>
                            <span
                                class="block font-semibold text-neutral-900"
                            >
                                Final Challenge Aktif
                            </span>

                            <span
                                class="mt-1 block text-sm leading-6 text-neutral-500"
                            >
                                Jika dicentang, Final Challenge
                                dapat ditampilkan dan dikerjakan
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
                            'teacher.missions.final-challenge.index',
                            $mission
                        ) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-5 py-3 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-amber-600"
                    >
                        Tambahkan Final Challenge
                        <span>+</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts::app>
