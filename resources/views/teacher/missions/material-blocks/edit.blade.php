<x-layouts::app :title="__('Edit Blok Materi')">
    @php
        $blockTypes = [
            'heading' => 'Heading atau Judul',
            'paragraph' => 'Paragraf',
            'code' => 'Kode Program',
            'image' => 'Gambar',
            'checkpoint' => 'Checkpoint',
        ];
    @endphp

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
                            Edit Blok Materi
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                        >
                            Perbarui jenis, judul, isi, urutan,
                            waktu baca minimum, dan status blok.
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

        {{-- Informasi blok --}}
        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-sm"
                    >
                        {{ $block->sequence }}
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-blue-600"
                        >
                            Blok yang Diedit
                        </p>

                        <h2
                            class="mt-1 text-lg font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $block->title
                                ?: ucfirst($block->type) }}
                        </h2>

                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            Bagian:
                            {{ $section->title }}
                        </p>
                    </div>
                </div>

                <span
                    class="w-fit rounded-full bg-blue-100 px-4 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                >
                    Blok {{ $block->sequence }}
                </span>
            </div>
        </section>

        {{-- Form edit --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Data Blok
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Informasi Materi
            </h2>

            <p
                class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
            >
                Perubahan yang disimpan akan digunakan pada halaman
                Materi Prakelas peserta didik.
            </p>

            <form
                method="POST"
                action="{{ route(
                    'teacher.missions.material-blocks.update',
                    [
                        'mission' => $mission,
                        'block' => $block,
                    ]
                ) }}"
                class="mt-6 space-y-6"
            >
                @csrf
                @method('PUT')

                {{-- Jenis blok --}}
                <div>
                    <label
                        for="type"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Jenis Blok
                    </label>

                    <select
                        id="type"
                        name="type"
                        required
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >
                        @foreach ($blockTypes as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'type',
                                        $block->type
                                    ) === $value
                                )
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @error('type')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Judul --}}
                <div>
                    <label
                        for="title"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Judul Blok
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old(
                            'title',
                            $block->title
                        ) }}"
                        maxlength="150"
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Judul boleh dikosongkan untuk blok yang tidak
                        memerlukan judul khusus.
                    </p>

                    @error('title')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Isi materi --}}
                <div>
                    <label
                        for="content"
                        class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                    >
                        Isi Blok
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="10"
                        class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 font-mono text-sm leading-6 text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                    >{{ old(
                        'content',
                        $block->content
                    ) }}</textarea>

                    <p
                        class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                    >
                        Untuk jenis kode program, isi dapat berupa
                        potongan kode. Untuk paragraf, isi berupa
                        teks materi pembelajaran.
                    </p>

                    @error('content')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Bahasa kode dan path media --}}
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label
                            for="code_language"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Bahasa Kode
                        </label>

                        <input
                            id="code_language"
                            name="code_language"
                            type="text"
                            value="{{ old(
                                'code_language',
                                $block->code_language
                            ) }}"
                            placeholder="Contoh: php, python, javascript"
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                        >

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Hanya digunakan apabila jenis blok
                            adalah Kode Program.
                        </p>

                        @error('code_language')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="media_path"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Path atau URL Gambar
                        </label>

                        <input
                            id="media_path"
                            name="media_path"
                            type="text"
                            value="{{ old(
                                'media_path',
                                $block->media_path
                            ) }}"
                            placeholder="Contoh: materials/perulangan.png"
                            class="mt-2 w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                        >

                        <p
                            class="mt-2 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            Hanya digunakan apabila jenis blok
                            adalah Gambar.
                        </p>

                        @error('media_path')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Urutan dan waktu baca --}}
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label
                            for="sequence"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Urutan Blok
                        </label>

                        <input
                            id="sequence"
                            name="sequence"
                            type="number"
                            min="1"
                            value="{{ old(
                                'sequence',
                                $block->sequence
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
                            for="minimum_read_seconds"
                            class="text-sm font-semibold text-neutral-800 dark:text-neutral-200"
                        >
                            Waktu Baca Minimum
                        </label>

                        <div class="relative mt-2">
                            <input
                                id="minimum_read_seconds"
                                name="minimum_read_seconds"
                                type="number"
                                min="0"
                                max="3600"
                                value="{{ old(
                                    'minimum_read_seconds',
                                    $block->minimum_read_seconds
                                ) }}"
                                required
                                class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 pr-20 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:focus:ring-blue-950"
                            >

                            <span
                                class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm text-neutral-500"
                            >
                                detik
                            </span>
                        </div>

                        @error('minimum_read_seconds')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Status blok --}}
                <div class="grid gap-4 md:grid-cols-2">
                    <div
                        class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-700 dark:bg-neutral-800/60"
                    >
                        <label
                            for="is_required"
                            class="flex cursor-pointer items-start gap-4"
                        >
                            <input
                                id="is_required"
                                name="is_required"
                                type="checkbox"
                                value="1"
                                @checked(
                                    old(
                                        'is_required',
                                        $block->is_required
                                    )
                                )
                                class="mt-1 h-5 w-5 rounded border-neutral-300 text-blue-600 focus:ring-blue-500"
                            >

                            <span>
                                <span
                                    class="block font-semibold text-neutral-900 dark:text-white"
                                >
                                    Blok Wajib
                                </span>

                                <span
                                    class="mt-1 block text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                                >
                                    Peserta didik wajib menyelesaikan
                                    blok ini.
                                </span>
                            </span>
                        </label>
                    </div>

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
                                        $block->is_active
                                    )
                                )
                                class="mt-1 h-5 w-5 rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500"
                            >

                            <span>
                                <span
                                    class="block font-semibold text-neutral-900 dark:text-white"
                                >
                                    Blok Aktif
                                </span>

                                <span
                                    class="mt-1 block text-sm leading-6 text-neutral-500 dark:text-neutral-400"
                                >
                                    Blok tampil pada Materi Prakelas
                                    peserta didik.
                                </span>
                            </span>
                        </label>
                    </div>
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
