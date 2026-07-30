<x-layouts::app :title="__('Edit Bagian Materi')">
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
                            Edit Bagian Materi
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Perbarui nama, deskripsi, urutan,
                            dan status bagian materi pembelajaran.
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

        {{-- Informasi bagian --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-sm"
                    >
                        {{ $section->sequence }}
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                        >
                            Bagian yang Diedit
                        </p>

                        <h2
                            class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $section->title }}
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
                        class="rounded-full bg-blue-100 px-4 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                    >
                        Bagian {{ $section->sequence }}
                    </span>

                    <span
                        class="rounded-full bg-violet-100 px-4 py-2 text-xs font-semibold text-violet-700 dark:bg-violet-950 dark:text-violet-300"
                    >
                        {{ $section->blocks_count }} blok
                    </span>
                </div>
            </div>
        </section>

        {{-- Form edit --}}
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
                Slug bagian dikunci agar struktur materi dan
                tautan pembelajaran tetap aman.
            </p>

            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.material-sections.update',
                    [
                        'mission' => $mission,
                        'section' => $section,
                    ]
                ) }}"
                class="mt-6 space-y-6"
            >
                @csrf
                @method('PUT')

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
                        value="{{ old(
                            'title',
                            $section->title
                        ) }}"
                        maxlength="150"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label
                        for="slug"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Slug Bagian
                    </label>

                    <input
                        id="slug"
                        type="text"
                        value="{{ $section->slug }}"
                        readonly
                        class="mt-2 w-full cursor-not-allowed rounded-xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400"
                    >

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Slug tidak dapat diubah karena digunakan
                        dalam struktur materi pembelajaran.
                    </p>
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
                    >{{ old(
                        'description',
                        $section->description
                    ) }}</textarea>

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Jelaskan secara singkat materi yang
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
                            $section->sequence
                        ) }}"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Urutan menentukan posisi bagian pada
                        halaman Materi Prakelas peserta didik.
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
                                    $section->is_active
                                )
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
                                Jika dicentang, bagian dan seluruh
                                blok aktif di dalamnya dapat tampil
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
                        Simpan Perubahan
                        <span>✓</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts::app>
