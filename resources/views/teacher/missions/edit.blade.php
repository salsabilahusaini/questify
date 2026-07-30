<x-layouts::app :title="__('Edit Misi Pembelajaran')">
    <div class="quest-page">
        {{-- Header --}}
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

                            Pengaturan Misi
                        </span>

                        <h1
                            class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                        >
                            Edit Misi Pembelajaran
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Perbarui informasi dasar, XP, urutan,
                            dan status publikasi misi.
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
                        Kembali ke Detail
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
                    {{ $mission->sequence }}
                </div>

                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                    >
                        Misi yang Diedit
                    </p>

                    <h2
                        class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                    >
                        {{ $mission->title }}
                    </h2>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ $mission->slug }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Form edit --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Informasi Dasar
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Data Misi Pembelajaran
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Slug tidak diubah agar tautan misi peserta didik
                tetap aman dan tidak terputus.
            </p>

            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.update',
                    $mission
                ) }}"
                class="mt-6 space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- Judul --}}
                <div>
                    <label
                        for="title"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Judul Misi
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old(
                            'title',
                            $mission->title
                        ) }}"
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
                        Slug Misi
                    </label>

                    <input
                        id="slug"
                        type="text"
                        value="{{ $mission->slug }}"
                        readonly
                        class="mt-2 w-full cursor-not-allowed rounded-xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400"
                    >

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Slug dikunci karena digunakan pada URL
                        misi peserta didik.
                    </p>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label
                        for="description"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Deskripsi Misi
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-6 text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >{{ old(
                        'description',
                        $mission->description
                    ) }}</textarea>

                    @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Urutan dan XP --}}
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label
                            for="sequence"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Urutan Misi
                        </label>

                        <input
                            id="sequence"
                            name="sequence"
                            type="number"
                            min="1"
                            value="{{ old(
                                'sequence',
                                $mission->sequence
                            ) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                        >

                        @error('sequence')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="xp_reward"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Hadiah XP
                        </label>

                        <input
                            id="xp_reward"
                            name="xp_reward"
                            type="number"
                            min="0"
                            max="10000"
                            value="{{ old(
                                'xp_reward',
                                $mission->xp_reward
                            ) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                        >

                        @error('xp_reward')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Perubahan XP berlaku untuk penyelesaian
                            misi berikutnya. XP yang sudah diterima
                            siswa tidak berubah.
                        </p>
                    </div>
                </div>

                {{-- Status publikasi --}}
                <div
                    class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-700 dark:bg-neutral-800/60"
                >
                    <label
                        for="is_published"
                        class="flex cursor-pointer items-start gap-4"
                    >
                        <input
                            id="is_published"
                            name="is_published"
                            type="checkbox"
                            value="1"
                            @checked(
                                old(
                                    'is_published',
                                    $mission->is_published
                                )
                            )
                            class="mt-1 h-5 w-5 rounded border-neutral-300 text-blue-600 focus:ring-blue-500"
                        >

                        <span>
                            <span
                                class="block font-semibold text-neutral-900 dark:text-white"
                            >
                                Publikasikan Misi
                            </span>

                            <span
                                class="mt-1 block text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                            >
                                Jika dicentang, misi dapat dilihat
                                dan dikerjakan peserta didik.
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
