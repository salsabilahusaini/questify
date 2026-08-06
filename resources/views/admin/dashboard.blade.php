<x-layouts::app :title="__('Dashboard Admin')">
    <div class="quest-page">

        {{-- Hero / Sambutan --}}
        <section class="quest-hero">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-quest-500"></span>
                        DASHBOARD ADMIN
                    </div>

                    <h1 class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white">
                        Selamat datang,
                        <span class="bg-gradient-to-r from-quest-500 to-quest-purple bg-clip-text text-transparent">
                            {{ auth()->user()->name }}!
                        </span>
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300">
                        Kelola pengguna, materi pembelajaran, serta aktivitas
                        pembelajaran pada platform Questify dalam satu tempat.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="quest-button-primary"
                        >
                            <svg
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v6M22 11h-6" />
                            </svg>

                            Kelola Pengguna
                        </a>

                        <span class="quest-badge">
                            Sistem berjalan normal
                        </span>
                    </div>
                </div>

                {{-- Dekorasi hero --}}
                <div class="relative hidden h-40 w-56 shrink-0 lg:block">
                    <div class="absolute right-2 top-1 size-28 rounded-3xl bg-gradient-to-br from-quest-500 to-quest-900 shadow-2xl shadow-blue-500/30 rotate-6"></div>

                    <div class="absolute bottom-0 left-2 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/30 bg-white/70 shadow-xl backdrop-blur dark:bg-neutral-900/70">
                        <svg
                            class="size-14 text-quest-600 dark:text-quest-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M12 3 3 8l9 5 9-5-9-5Z" />
                            <path d="m3 12 9 5 9-5" />
                            <path d="m3 16 9 5 9-5" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Ringkasan Statistik --}}
        <section>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-neutral-950 dark:text-white">
                        Ringkasan Platform
                    </h2>

                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        Informasi utama pengguna dan konten Questify.
                    </p>
                </div>

                <span class="hidden text-xs font-medium text-neutral-400 sm:inline">
                    Diperbarui otomatis
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Total Pengguna --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950 dark:text-blue-300">
                                Semua role
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Total Pengguna
                        </p>

                        <p class="mt-1 text-4xl font-bold tracking-tight text-neutral-950 dark:text-white">
                            {{ $totalUsers ?? 0 }}
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            Admin, guru, dan peserta didik
                        </p>
                    </div>
                </article>

                {{-- Guru --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="m3 10 9-5 9 5-9 5-9-5Z" />
                                    <path d="m7 12.5v4.2c0 1.3 2.2 2.3 5 2.3s5-1 5-2.3v-4.2" />
                                    <path d="M21 10v6" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-600 dark:bg-violet-950 dark:text-violet-300">
                                Pengajar
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Jumlah Guru
                        </p>

                        <p class="mt-1 text-4xl font-bold tracking-tight text-neutral-950 dark:text-white">
                            {{ $totalTeachers ?? 0 }}
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            Guru yang telah terdaftar
                        </p>
                    </div>
                </article>

                {{-- Peserta Didik --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M4 21a8 8 0 0 1 16 0" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                                Pembelajar
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
                            Peserta Didik
                        </p>

                        <p class="mt-1 text-4xl font-bold tracking-tight text-neutral-950 dark:text-white">
                            {{ $totalStudents ?? 0 }}
                        </p>

                        <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                            Peserta didik yang telah terdaftar
                        </p>
                    </div>
                </article>

                {{-- Modul --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex size-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-300">
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
                                </svg>
                            </div>

                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:bg-amber-950 dark:text-amber-300">
                                Konten
                            </span>
                        </div>

                        <p class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400">
    Misi Pembelajaran
</p>

<p class="mt-1 text-4xl font-bold tracking-tight text-neutral-950 dark:text-white">
    {{ $totalMissions }}
</p>

<p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
    Misi yang telah dibuat
</p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Pengelolaan Questify --}}
        <section class="quest-card p-6 sm:p-7">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="quest-eyebrow">
                        PUSAT KONTROL
                    </span>

                    <h2 class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white">
                        Pengelolaan Questify
                    </h2>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                        Akses fitur pengelolaan pengguna, materi pembelajaran,
                        dan monitoring aktivitas dari satu dashboard.
                    </p>
                </div>

                <span class="text-xs font-medium text-neutral-400">
                    Admin Workspace
                </span>
            </div>

            <div class="mt-7 grid gap-4 lg:grid-cols-3">

                {{-- Kelola Pengguna --}}
                <a
                    href="{{ route('admin.users.index') }}"
                    class="group relative overflow-hidden rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-5 transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/10 dark:border-blue-900/60 dark:from-blue-950/50 dark:to-neutral-900"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/25">
                            <svg
                                class="size-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M19 8v6M22 11h-6" />
                            </svg>
                        </div>

                        <span class="text-blue-600 transition group-hover:translate-x-1 dark:text-blue-300">
                            →
                        </span>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-neutral-950 dark:text-white">
                        Kelola Pengguna
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400">
                        Tambah, edit, dan kelola akun Admin, Guru, serta Peserta Didik.
                    </p>

                    <span class="mt-5 inline-flex text-sm font-semibold text-blue-600 dark:text-blue-300">
                        Buka pengelolaan
                    </span>
                </a>

                {{-- Kelola Modul --}}
                <article class="relative overflow-hidden rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-5 dark:border-violet-900/60 dark:from-violet-950/40 dark:to-neutral-900">
                    <div class="flex items-start justify-between">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-lg shadow-violet-600/25">
                            <svg
                                class="size-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
                                <path d="M9 7h7M9 11h7" />
                            </svg>
                        </div>

                        <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-600 dark:bg-violet-950 dark:text-violet-300">
                            Segera
                        </span>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-neutral-950 dark:text-white">
                        Kelola Modul
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400">
                        Atur materi, misi, latihan mikro, kuis, dan tantangan pembelajaran.
                    </p>

                    <span class="mt-5 inline-flex text-sm font-semibold text-violet-600 dark:text-violet-300">
                        Segera tersedia
                    </span>
                </article>

                {{-- Monitoring --}}
                <article class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 dark:border-emerald-900/60 dark:from-emerald-950/40 dark:to-neutral-900">
                    <div class="flex items-start justify-between">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/25">
                            <svg
                                class="size-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M3 3v18h18" />
                                <path d="m7 15 4-4 3 3 5-7" />
                            </svg>
                        </div>

                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                            Segera
                        </span>
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-neutral-950 dark:text-white">
                        Monitoring Aktivitas
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-400">
                        Pantau riwayat akses, kesiapan belajar, progres, dan hasil pembelajaran.
                    </p>

                    <span class="mt-5 inline-flex text-sm font-semibold text-emerald-600 dark:text-emerald-300">
                        Segera tersedia
                    </span>
                </article>
            </div>
        </section>

        {{-- Footer mini --}}
        <section class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400">
            <p>
                Questify — Learn by Doing. Think by Solving.
            </p>

            <span class="font-medium text-quest-600 dark:text-quest-300">
                Admin Dashboard
            </span>
        </section>
    </div>
</x-layouts::app>
