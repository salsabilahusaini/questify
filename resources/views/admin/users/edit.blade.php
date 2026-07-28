<x-layouts::app :title="__('Edit Pengguna')">
    <div class="quest-page">

        {{-- Hero --}}
        <section class="quest-hero">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-quest-500"></span>
                        ADMINISTRASI PENGGUNA
                    </div>

                    <h1 class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white">
                        Edit
                        <span class="bg-gradient-to-r from-quest-500 to-quest-purple bg-clip-text text-transparent">
                            Data Pengguna
                        </span>
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300">
                        Perbarui identitas, alamat email, role, atau password
                        pengguna Questify yang dipilih.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-neutral-300 bg-white/70 px-4 py-3 text-sm font-semibold text-neutral-700 transition hover:border-blue-300 hover:bg-blue-50 dark:border-neutral-700 dark:bg-neutral-900/70 dark:text-neutral-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/40"
                        >
                            <svg
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m15 18-6-6 6-6" />
                            </svg>

                            Kembali ke Daftar Pengguna
                        </a>

                        @if (auth()->id() === $user->id)
                            <span class="quest-badge">
                                Akun yang sedang digunakan
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Dekorasi --}}
                <div class="relative hidden h-40 w-56 shrink-0 lg:block">
                    <div class="absolute right-2 top-1 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-violet-500 to-blue-900 shadow-2xl shadow-violet-500/30"></div>

                    <div class="absolute bottom-0 left-2 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/30 bg-white/75 shadow-xl backdrop-blur dark:bg-neutral-900/75">
                        <svg
                            class="size-14 text-blue-600 dark:text-blue-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M12 20h9" />
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Identitas pengguna --}}
        <section class="quest-card p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-900 text-base font-bold text-white shadow-lg shadow-blue-500/20">
                        {{ $user->initials() }}
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold text-neutral-950 dark:text-white">
                            {{ $user->name }}
                        </p>

                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>

                <div>
                    @if ($user->isAdmin())
                        <span class="inline-flex rounded-full bg-violet-100 px-3 py-1.5 text-xs font-bold text-violet-700 dark:bg-violet-950 dark:text-violet-300">
                            Administrator
                        </span>
                    @elseif ($user->isTeacher())
                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            Guru
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            Peserta Didik
                        </span>
                    @endif
                </div>
            </div>
        </section>

        {{-- Form --}}
        <section class="quest-card overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-6 dark:border-neutral-800">
                <span class="quest-eyebrow">
                    FORM PERUBAHAN
                </span>

                <h2 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
                    Informasi Akun
                </h2>

                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Ubah data yang diperlukan, lalu simpan perubahan.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.users.update', $user) }}"
                class="p-6 sm:p-8"
            >
                @csrf
                @method('PUT')

                {{-- Ringkasan kesalahan --}}
                @if ($errors->any())
                    <div class="mb-7 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 dark:border-red-900/70 dark:bg-red-950/50 dark:text-red-300">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900">
                            <svg
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 8v5" />
                                <path d="M12 17h.01" />
                            </svg>
                        </div>

                        <div>
                            <p class="font-bold">
                                Perubahan belum dapat disimpan
                            </p>

                            <p class="mt-1">
                                Periksa kembali kolom yang ditandai.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="grid gap-6 lg:grid-cols-2">

                    {{-- Nama --}}
                    <div class="lg:col-span-2">
                        <label
                            for="name"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Nama Lengkap
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Nama akan ditampilkan pada dashboard pengguna.
                        </p>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <svg
                                    class="size-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" />
                                </svg>
                            </div>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $user->name) }}"
                                required
                                autofocus
                                autocomplete="name"
                                class="block w-full rounded-xl border border-neutral-300 bg-white py-3.5 pr-4 pl-12 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                        </div>

                        @error('name')
                            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="lg:col-span-2">
                        <label
                            for="email"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Alamat Email
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Email digunakan pengguna untuk masuk ke Questify.
                        </p>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <svg
                                    class="size-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path d="m3 7 9 6 9-6" />
                                </svg>
                            </div>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                autocomplete="email"
                                class="block w-full rounded-xl border border-neutral-300 bg-white py-3.5 pr-4 pl-12 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                        </div>

                        @error('email')
                            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="lg:col-span-2">
                        <label
                            for="role"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Role Pengguna
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Perubahan role akan mengubah halaman dan fitur yang dapat diakses.
                        </p>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <svg
                                    class="size-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M12 3 4 7v5c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V7l-8-4Z" />
                                </svg>
                            </div>

                            <select
                                id="role"
                                name="role"
                                required
                                class="block w-full appearance-none rounded-xl border border-neutral-300 bg-white py-3.5 pr-12 pl-12 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                                <option
                                    value="admin"
                                    @selected(old('role', $user->role) === 'admin')
                                >
                                    Administrator
                                </option>

                                <option
                                    value="teacher"
                                    @selected(old('role', $user->role) === 'teacher')
                                >
                                    Guru
                                </option>

                                <option
                                    value="student"
                                    @selected(old('role', $user->role) === 'student')
                                >
                                    Peserta Didik
                                </option>
                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-neutral-400">
                                <svg
                                    class="size-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>

                        @error('role')
                            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password baru --}}
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Password Baru
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Kosongkan jika password tidak ingin diubah.
                        </p>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <svg
                                    class="size-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <rect x="4" y="10" width="16" height="10" rx="2" />
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                                </svg>
                            </div>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Kosongkan jika tidak diubah"
                                class="block w-full rounded-xl border border-neutral-300 bg-white py-3.5 pr-4 pl-12 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                        </div>

                        @error('password')
                            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Konfirmasi password --}}
                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Konfirmasi Password Baru
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Isi hanya apabila password baru digunakan.
                        </p>

                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-neutral-400">
                                <svg
                                    class="size-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m5 12 4 4L19 6" />
                                </svg>
                            </div>

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Ulangi password baru"
                                class="block w-full rounded-xl border border-neutral-300 bg-white py-3.5 pr-4 pl-12 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                        </div>
                    </div>
                </div>

                {{-- Informasi keamanan --}}
                <div class="mt-7 rounded-2xl border border-amber-200 bg-amber-50/70 p-4 dark:border-amber-900/60 dark:bg-amber-950/30">
                    <div class="flex items-start gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300">
                            <svg
                                class="size-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 9v4" />
                                <path d="M12 17h.01" />
                                <path d="M10.3 3.7 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-amber-900 dark:text-amber-200">
                                Perhatikan perubahan akses
                            </p>

                            <p class="mt-1 text-sm leading-6 text-amber-700 dark:text-amber-300">
                                Mengubah role akan memengaruhi dashboard dan hak
                                akses pengguna. Password lama tetap digunakan
                                apabila kolom password baru dikosongkan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-neutral-200 pt-6 sm:flex-row sm:justify-end dark:border-neutral-800">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-5 py-3 text-sm font-bold text-neutral-700 transition hover:border-neutral-400 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="quest-button-primary px-6"
                    >
                        <svg
                            class="size-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" />
                            <path d="M17 21v-8H7v8" />
                            <path d="M7 3v5h8" />
                        </svg>

                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        {{-- Footer --}}
        <section class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400">
            <p>
                Questify — Learn by Doing. Think by Solving.
            </p>

            <span class="font-medium text-quest-600 dark:text-quest-300">
                Edit User
            </span>
        </section>
    </div>
</x-layouts::app>
