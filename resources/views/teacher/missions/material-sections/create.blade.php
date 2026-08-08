<x-layouts::app :title="__('Tambah Bagian Materi')">
    <div class="quest-page">
        {{-- Header halaman --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <div
                    class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-violet-500"
                            ></span>

                            Pengelolaan Materi
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Tambah Bagian Materi
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Tambahkan bagian baru untuk menyusun
                            Materi Prakelas pada misi
                            {{ $mission->title }}.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'teacher.missions.show',
                            $mission
                        ) }}"
                        class="inline-flex w-fit items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                    >
                        <span>←</span>
                        Kembali ke Detail Misi
                    </a>
                </div>
            </div>
        </section>

        {{-- Informasi misi --}}
        <section class="quest-card p-6">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-sm"
                >
                    {{ old('sequence', $nextSequence) }}
                </div>

                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Bagian Materi Baru
                    </p>

                    <h2
                        class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        Materi Prakelas
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
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Data Bagian Materi
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Informasi Bagian
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Slug akan dibuat otomatis dari judul dan
                disesuaikan agar tetap unik pada misi ini.
            </p>
            @if (session('error'))
    <div
        class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
    >
        {{ session('error') }}
    </div>
@endif

            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.material-sections.store',
                    $mission
                ) }}"
                class="mt-6 space-y-6"
            >
                @csrf

                {{-- Judul bagian --}}
                <div>
                    <label
                        for="title"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Judul Bagian
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title') }}"
                        maxlength="150"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Contoh: Pengertian dan Konsep Dasar
                        Perulangan.
                    </p>

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label
                        for="description"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Deskripsi Bagian
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        maxlength="2000"
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >{{ old('description') }}</textarea>

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Jelaskan secara singkat materi yang akan
                        dipelajari pada bagian ini.
                    </p>

                    @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Urutan bagian --}}
                <div>
                    <label
                        for="sequence"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Urutan Bagian
                    </label>

                    <input
                        id="sequence"
                        name="sequence"
                        type="number"
                        min="1"
                        value="{{ old(
                            'sequence',
                            $nextSequence
                        ) }}"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Urutan berikutnya telah ditentukan
                        otomatis dan dapat disesuaikan selama
                        belum digunakan bagian lain.
                    </p>

                    @error('sequence')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status aktif --}}
                <div
                    class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-700 dark:bg-neutral-800/60"
                >
                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
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
                                old('is_active', true)
                            )
                            class="mt-1 h-5 w-5 rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500"
                        >

                        <span>
                            <span
                                class="block font-semibold text-neutral-900 dark:text-white"
                            >
                                Bagian Aktif
                            </span>

                            <span
                                class="mt-1 block text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                            >
                                Jika dicentang, bagian dan blok
                                aktif di dalamnya dapat ditampilkan
                                pada Materi Prakelas peserta didik.
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Tombol --}}
                <div
                    class="flex flex-col-reverse gap-3 border-t border-neutral-200 pt-6 sm:flex-row sm:justify-end dark:border-neutral-700"
                >
                    <a
                        href="{{ route(
                            'teacher.missions.show',
                            $mission
                        ) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-5 py-3 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Tambahkan Bagian
                        <span>+</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts::app>
