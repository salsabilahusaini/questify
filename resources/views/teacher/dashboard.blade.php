<x-layouts::app :title="__('Dashboard Guru')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Sambutan --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Dashboard Guru
            </p>

            <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                Selamat datang, {{ auth()->user()->name }}!
            </h1>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Pantau kesiapan belajar, perkembangan misi, dan hasil pembelajaran
                peserta didik melalui platform Questify.
            </p>
        </section>

        {{-- Ringkasan --}}
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Peserta Didik
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Peserta didik yang terdaftar
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Modul Aktif
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Modul yang tersedia
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Siap Belajar
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Siswa yang menyelesaikan kegiatan prakelас
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Perlu Perhatian
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Siswa dengan aktivitas belum lengkap
                </p>
            </div>

        </section>

        {{-- Menu utama guru --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                Aktivitas Pembelajaran
            </h2>

            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Fitur pembelajaran akan dihubungkan secara bertahap.
            </p>

            <div class="mt-5 grid gap-4 md:grid-cols-3">

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">
                        Kelola Materi dan Misi
                    </h3>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Mengatur materi, latihan mikro, kuis, dan tantangan.
                    </p>

                    <span class="mt-4 inline-block text-xs font-medium text-blue-600 dark:text-blue-400">
                        Segera tersedia
                    </span>
                </div>

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">
                        Monitoring Peserta Didik
                    </h3>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Memeriksa riwayat akses, waktu belajar, dan penyelesaian misi.
                    </p>

                    <span class="mt-4 inline-block text-xs font-medium text-blue-600 dark:text-blue-400">
                        Segera tersedia
                    </span>
                </div>

                <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">
                        Hasil Pembelajaran
                    </h3>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Melihat hasil kuis, tantangan, dan perkembangan belajar siswa.
                    </p>

                    <span class="mt-4 inline-block text-xs font-medium text-blue-600 dark:text-blue-400">
                        Segera tersedia
                    </span>
                </div>

            </div>
        </section>

    </div>
</x-layouts::app>
