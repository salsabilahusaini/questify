<x-layouts::app :title="__('Tambah Pengguna')">
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
                        Tambah
                        <span class="bg-gradient-to-r from-quest-500 to-quest-purple bg-clip-text text-transparent">
                            Pengguna Baru
                        </span>
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300">
                        Buat akun baru untuk Administrator, Guru, atau Peserta
                        Didik yang akan menggunakan platform Questify.
                    </p>

                    <div class="mt-6">
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
                    </div>
                </div>

                {{-- Dekorasi --}}
                <div class="relative hidden h-40 w-56 shrink-0 lg:block">
                    <div class="absolute right-2 top-1 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-blue-500 to-blue-900 shadow-2xl shadow-blue-500/30"></div>

                    <div class="absolute bottom-0 left-2 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/30 bg-white/75 shadow-xl backdrop-blur dark:bg-neutral-900/75">
                        <svg
                            class="size-14 text-blue-600 dark:text-blue-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M19 8v6M22 11h-6" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Form --}}
        <section class="quest-card overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-6 dark:border-neutral-800">
                <span class="quest-eyebrow">
                    FORM PENGGUNA
                </span>

                <h2 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
                    Informasi Akun
                </h2>

                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Lengkapi data berikut untuk membuat akun Questify baru.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('admin.users.store') }}"
                class="p-6 sm:p-8"
            >
                @csrf

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
                                Data belum dapat disimpan
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
                            Masukkan nama sesuai identitas pengguna.
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
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Contoh: Salsabila Husaini"
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
                            Email digunakan untuk masuk ke akun Questify.
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
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="nama@questify.test"
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
                            Role menentukan halaman dan fitur yang dapat diakses.
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
                                <option value="">
                                    Pilih role pengguna
                                </option>

                                <option
                                    value="admin"
                                    @selected(old('role') === 'admin')
                                >
                                    Administrator
                                </option>

                                <option
                                    value="teacher"
                                    @selected(old('role') === 'teacher')
                                >
                                    Guru
                                </option>

                                <option
                                    value="student"
                                    @selected(old('role') === 'student')
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

                    {{-- Password --}}
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Password
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Gunakan minimal delapan karakter.
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
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="block w-full rounded-xl border border-neutral-300 bg-white py-3.5 pr-4 pl-12 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                        </div>

                        @error('password')
                            <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-bold text-neutral-800 dark:text-neutral-200"
                        >
                            Konfirmasi Password
                        </label>

                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                            Masukkan kembali password yang sama.
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
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                                class="block w-full rounded-xl border border-neutral-300 bg-white py-3.5 pr-4 pl-12 text-sm text-neutral-900 outline-none transition placeholder:text-neutral-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                            >
                        </div>
                    </div>
                </div>

                {{-- Informasi --}}
                <div class="mt-7 rounded-2xl border border-blue-200 bg-blue-50/70 p-4 dark:border-blue-900/60 dark:bg-blue-950/30">
                    <div class="flex items-start gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                            <svg
                                class="size-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 11v5" />
                                <path d="M12 8h.01" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-sm font-bold text-blue-900 dark:text-blue-200">
                                Informasi akun
                            </p>

                            <p class="mt-1 text-sm leading-6 text-blue-700 dark:text-blue-300">
                                Setelah akun dibuat, pengguna dapat masuk menggunakan
                                email dan password yang telah ditentukan.
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
                            <path d="M12 5v14M5 12h14" />
                        </svg>

                        Simpan Pengguna
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
                Create User
            </span>
        </section>
    </div>
</x-layouts::app>
