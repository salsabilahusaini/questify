<x-layouts::app :title="__('Misi Pembelajaran')">
    <div class="quest-page">
        {{-- Hero --}}
        <section class="quest-hero">
            <div class="relative z-10 flex flex-col gap-7 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-violet-500"></span>
                        Misi Pembelajaran
                    </div>

                    <h1 class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white">
                        Mulai perjalanan belajar
                        <span class="bg-gradient-to-r from-blue-500 via-violet-500 to-fuchsia-500 bg-clip-text text-transparent">
                            Questify
                        </span>
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300">
                        Selesaikan setiap kegiatan secara berurutan untuk
                        memperoleh XP, membuka tantangan, dan melanjutkan
                        perjalanan pembelajaran.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
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

                        <span class="rounded-full bg-violet-100 px-4 py-2 text-xs font-semibold text-violet-700 dark:bg-violet-950 dark:text-violet-300">
                            {{ $missions->count() }} misi tersedia
                        </span>
                    </div>
                </div>

                <div class="relative hidden h-44 w-60 shrink-0 lg:block">
                    <div class="absolute right-2 top-2 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-violet-500 to-blue-700 shadow-2xl shadow-violet-500/30"></div>

                    <div class="absolute bottom-1 left-3 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/40 bg-white/85 shadow-xl backdrop-blur dark:bg-neutral-900/85">
                        <svg
                            class="size-14 text-violet-600 dark:text-violet-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
                            <path d="m9 10 2 2 4-4" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Informasi alur --}}
        <section>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-950 dark:text-white">
                        Daftar Misi
                    </h2>

                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        Ikuti misi berdasarkan urutan yang telah disediakan.
                    </p>
                </div>

                <span class="text-xs font-medium text-neutral-400">
                    Progress diperbarui otomatis
                </span>
            </div>

            <div class="mt-5 space-y-5">
                @forelse ($missions as $mission)
                    <article class="quest-card overflow-hidden">
                        <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1fr)_240px]">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                        Misi {{ $mission->sequence }}
                                    </span>

                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        Tersedia
                                    </span>
                                </div>

                                <h3 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
                                    {{ $mission->title }}
                                </h3>

                                <p class="mt-3 max-w-3xl text-sm leading-7 text-neutral-500 dark:text-neutral-400">
                                    {{ $mission->description }}
                                </p>

                                <div class="mt-5">
                                    <p class="text-xs font-bold uppercase tracking-wide text-neutral-400">
                                        Alur kegiatan
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach (['Pretest', 'Materi', 'Latihan Mikro', 'Kuis', 'Final Challenge'] as $step)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-neutral-50 px-3 py-2 text-xs font-medium text-neutral-600 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                                <span class="size-1.5 rounded-full bg-violet-500"></span>
                                                {{ $step }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-6">
    <a
    href="{{ route('student.missions.show', ['mission' => $mission->slug]) }}"
    class="quest-button-primary"
    wire:navigate
>
    <svg
        class="size-4"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
    >
        <path d="m8 5 11 7-11 7Z" />
    </svg>

    Buka Detail Misi
</a>
                                </div>
                            </div>

                            <aside class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-700 dark:bg-neutral-800/70">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                        Progress
                                    </p>

                                    <div class="mt-2 flex items-end justify-between">
                                        <p class="text-3xl font-bold text-neutral-950 dark:text-white">
                                            0%
                                        </p>

                                        <span class="text-xs font-medium text-neutral-400">
                                            Belum dimulai
                                        </span>
                                    </div>

                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-700">
                                        <div class="h-full w-0 rounded-full bg-gradient-to-r from-blue-500 to-violet-500"></div>
                                    </div>
                                </div>

                                <div class="mt-6 border-t border-neutral-200 pt-5 dark:border-neutral-700">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                        Reward penyelesaian
                                    </p>

                                    <div class="mt-3 flex items-center gap-3">
                                        <div class="flex size-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-300">
                                            <svg
                                                class="size-6"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="m13 2-9 12h7l-1 8 9-12h-7Z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-2xl font-bold text-neutral-950 dark:text-white">
                                                {{ $mission->xp_reward }}
                                                <span class="text-base text-amber-500">XP</span>
                                            </p>

                                            <p class="text-xs text-neutral-400">
                                                Setelah misi selesai
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </article>
                @empty
                    <div class="quest-card p-8 text-center">
                        <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-neutral-100 text-neutral-400 dark:bg-neutral-800">
                            <svg
                                class="size-7"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
                            </svg>
                        </div>

                        <h3 class="mt-4 text-lg font-bold text-neutral-950 dark:text-white">
                            Belum ada misi yang tersedia
                        </h3>

                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                            Misi pembelajaran akan muncul setelah dipublikasikan oleh pengelola.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400">
            <p>
                Questify — Learn by Doing. Think by Solving.
            </p>

            <span class="font-medium text-violet-600 dark:text-violet-300">
                Student Mission Journey
            </span>
        </section>
    </div>
</x-layouts::app>
