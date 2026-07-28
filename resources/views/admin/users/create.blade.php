<x-layouts::app :title="__('Tambah Pengguna')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Judul halaman --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Administrasi Pengguna
            </p>

            <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                Tambah Pengguna
            </h1>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Buat akun baru untuk Admin, Guru, atau Peserta Didik Questify.
            </p>
        </section>

        {{-- Form tambah pengguna --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <form
                method="POST"
                action="{{ route('admin.users.store') }}"
                class="space-y-6"
            >
                @csrf

                {{-- Nama --}}
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Nama Lengkap
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Masukkan nama lengkap"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >

                    @error('name')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Email
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="nama@questify.test"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >

                    @error('email')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label
                        for="role"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Role Pengguna
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="">Pilih role pengguna</option>

                        <option value="admin" @selected(old('role') === 'admin')>
                            Admin
                        </option>

                        <option value="teacher" @selected(old('role') === 'teacher')>
                            Guru
                        </option>

                        <option value="student" @selected(old('role') === 'student')>
                            Peserta Didik
                        </option>
                    </select>

                    @error('role')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        placeholder="Minimal 8 karakter"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >

                    @error('password')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Konfirmasi password --}}
                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Konfirmasi Password
                    </label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="Masukkan kembali password"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                </div>

                {{-- Tombol --}}
                <div class="flex flex-col-reverse gap-3 border-t border-neutral-200 pt-6 sm:flex-row sm:justify-end dark:border-neutral-700">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-5 py-3 text-sm font-semibold text-neutral-700 transition hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </section>

    </div>
</x-layouts::app>
