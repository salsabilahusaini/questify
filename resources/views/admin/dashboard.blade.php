<x-layouts::app :title="__('Dashboard Admin')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Bagian sambutan --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Dashboard Admin
            </p>

            <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                Selamat datang, {{ auth()->user()->name }}!
            </h1>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Kelola pengguna, materi pembelajaran, serta aktivitas pembelajaran
                pada platform Questify.
            </p>
        </section>

        {{-- Ringkasan data --}}
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Total Pengguna
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalUsers }}
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Admin, guru, dan peserta didik
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Jumlah Guru
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalTeachers }}
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Guru yang terdaftar
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Peserta Didik
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalStudents }}
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Peserta didik yang terdaftar
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Modul Pembelajaran
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Modul yang tersedia
                </p>
            </div>

        </section>

        {{-- Menu pengelolaan --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Pengelolaan Questify
                </h2>

                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Fitur pengelolaan akan dihubungkan secara bertahap.
                </p>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-3">

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">
                        Kelola Pengguna
                    </h3>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Mengelola akun admin, guru, dan peserta didik.
                    </p>

                    <span class="mt-4 inline-block text-xs font-medium text-blue-600 dark:text-blue-400">
                        Segera tersedia
                    </span>
                </div>

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">
                        Kelola Modul
                    </h3>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Mengatur materi, misi, kuis, dan tantangan pembelajaran.
                    </p>

                    <span class="mt-4 inline-block text-xs font-medium text-blue-600 dark:text-blue-400">
                        Segera tersedia
                    </span>
                </div>

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">
                        Monitoring Aktivitas
                    </h3>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Melihat aktivitas dan perkembangan pembelajaran pengguna.
                    </p>

                    <span class="mt-4 inline-block text-xs font-medium text-blue-600 dark:text-blue-400">
                        Segera tersedia
                    </span>
                </div>

            </div>
        </section>

    </div>
</x-layouts::app>
