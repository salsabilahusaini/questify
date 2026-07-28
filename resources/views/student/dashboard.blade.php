<x-layouts::app :title="__('Dashboard Peserta Didik')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- Bagian sambutan --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Dashboard Peserta Didik
            </p>

            <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                Selamat datang, {{ auth()->user()->name }}!
            </h1>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Selesaikan misi pembelajaran, kumpulkan XP, dan tingkatkan
                kemampuan computational thinking bersama Questify.
            </p>
        </section>

        {{-- Ringkasan perkembangan --}}
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Misi Selesai
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Dari seluruh misi pembelajaran
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Total XP
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0 XP
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    XP yang telah dikumpulkan
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Level
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    Starter
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Tingkat perjalanan belajar saat ini
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Badge
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    0
                </p>

                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                    Penghargaan yang telah diperoleh
                </p>
            </div>

        </section>

        {{-- Misi pembelajaran --}}
        <section class="grid gap-6 xl:grid-cols-3">

            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900 xl:col-span-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                            Misi Pembelajaran
                        </h2>

                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                            Mulai perjalanan belajar dari materi yang tersedia.
                        </p>
                    </div>

                    <span class="w-fit rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                        Belum dimulai
                    </span>
                </div>

                <div class="mt-5 rounded-lg border border-neutral-200 p-5 dark:border-neutral-700">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                        Misi 1
                    </p>

                    <h3 class="mt-2 text-lg font-semibold text-neutral-900 dark:text-white">
                        Memahami Konsep Perulangan
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Pelajari pengertian, fungsi, komponen, kondisi berhenti,
                        dan kesalahan umum pada struktur perulangan.
                    </p>

                    <div class="mt-5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-neutral-500 dark:text-neutral-400">
                                Progres
                            </span>

                            <span class="font-medium text-neutral-700 dark:text-neutral-300">
                                0%
                            </span>
                        </div>

                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                            <div class="h-full w-0 rounded-full bg-blue-600"></div>
                        </div>
                    </div>

                    <button
                        type="button"
                        disabled
                        class="mt-5 inline-flex cursor-not-allowed items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white opacity-60"
                    >
                        Segera tersedia
                    </button>
                </div>
            </div>

            {{-- Status kesiapan --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Kesiapan Belajar
                </h2>

                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Selesaikan kegiatan prakelас sebelum pembelajaran tatap muka.
                </p>

                <div class="mt-5 space-y-4">
                    <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">
                            Pretest
                        </span>

                        <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                            Belum
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">
                            Materi Prakelas
                        </span>

                        <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                            Belum
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">
                            Latihan Mikro
                        </span>

                        <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                            Belum
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300">
                            Kuis Materi
                        </span>

                        <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                            Belum
                        </span>
                    </div>
                </div>

                <div class="mt-5 rounded-lg bg-neutral-100 p-4 dark:bg-neutral-800">
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                        Status saat ini
                    </p>

                    <p class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white">
                        Belum siap mengikuti pembelajaran
                    </p>
                </div>
            </div>

        </section>

    </div>
</x-layouts::app>
