<x-layouts::app :title="__('Edit Pengguna')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Administrasi Pengguna
            </p>

            <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                Edit Pengguna
            </h1>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Perbarui identitas, role, atau password pengguna Questify.
            </p>
        </section>

        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <form
                method="POST"
                action="{{ route('admin.users.update', $user) }}"
                class="space-y-6"
            >
                @csrf
                @method('PUT')

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
                        value="{{ old('name', $user->name) }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >

                    @error('name')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

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
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="email"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >

                    @error('email')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

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
                        <option
                            value="admin"
                            @selected(old('role', $user->role) === 'admin')
                        >
                            Admin
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

                    @error('role')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Password Baru
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Kosongkan apabila password tidak diubah"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >

                    <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                        Password lama tetap digunakan apabila bagian ini dikosongkan.
                    </p>

                    @error('password')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        Konfirmasi Password Baru
                    </label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Masukkan kembali password baru"
                        class="mt-2 block w-full rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                </div>

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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

    </div>
</x-layouts::app>
