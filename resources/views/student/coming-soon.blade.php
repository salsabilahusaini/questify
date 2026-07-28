<x-layouts::app :title="$title">
    <div class="quest-page">
        {{-- Hero --}}
        <section class="quest-hero">
            <div class="relative z-10 flex flex-col gap-7 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-violet-500"></span>
                        {{ $eyebrow }}
                    </div>

                    <h1 class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white">
                        {{ $title }}
                        <span class="bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500 bg-clip-text text-transparent">
                            Questify
                        </span>
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300">
                        {{ $description }}
                    </p>

                    <div class="mt-6">
                        <a
                            href="{{ route('student.dashboard') }}"
                            class="quest-button-primary"
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

                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>

                <div class="relative hidden h-44 w-60 shrink-0 lg:block">
                    <div class="absolute right-2 top-2 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-violet-500 to-blue-700 shadow-2xl shadow-violet-500/30"></div>

                    <div class="absolute bottom-1 left-3 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/40 bg-white/80 shadow-xl backdrop-blur dark:bg-neutral-900/80">
                        <svg
                            class="size-14 text-violet-600 dark:text-violet-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M12 6v6l4 2" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Informasi fitur --}}
        <section class="quest-card overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-6 dark:border-neutral-800">
                <span class="quest-eyebrow">
                    PENGEMBANGAN FITUR
                </span>

                <h2 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
                    Fitur sedang disiapkan
                </h2>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                    Halaman ini sudah terhubung dengan navigasi Questify.
                    Fungsi utama dan data pembelajaran akan ditambahkan pada
                    tahap pengembangan berikutnya.
                </p>
            </div>

            <div class="grid gap-4 p-6 md:grid-cols-3">
                <article class="rounded-2xl border border-blue-200 bg-blue-50/70 p-5 dark:border-blue-900/60 dark:bg-blue-950/30">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-blue-600 text-white">
                        <span class="font-bold">1</span>
                    </div>

                    <h3 class="mt-4 font-bold text-neutral-950 dark:text-white">
                        Struktur Halaman
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Halaman dan route sudah dibuat serta dapat diakses
                        berdasarkan role Peserta Didik.
                    </p>
                </article>

                <article class="rounded-2xl border border-violet-200 bg-violet-50/70 p-5 dark:border-violet-900/60 dark:bg-violet-950/30">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-violet-600 text-white">
                        <span class="font-bold">2</span>
                    </div>

                    <h3 class="mt-4 font-bold text-neutral-950 dark:text-white">
                        Integrasi Data
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Database dan logika fitur akan dihubungkan setelah
                        struktur pembelajaran selesai dibuat.
                    </p>
                </article>

                <article class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-emerald-600 text-white">
                        <span class="font-bold">3</span>
                    </div>

                    <h3 class="mt-4 font-bold text-neutral-950 dark:text-white">
                        Pengujian Fitur
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Setiap fitur akan diuji agar hak akses dan alur
                        pembelajaran berjalan dengan benar.
                    </p>
                </article>
            </div>
        </section>

        <section class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400">
            <p>
                Questify — Learn by Doing. Think by Solving.
            </p>

            <span class="font-medium text-quest-600 dark:text-quest-300">
                Student Learning Journey
            </span>
        </section>
    </div>
</x-layouts::app>
