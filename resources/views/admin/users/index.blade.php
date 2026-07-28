<x-layouts::app :title="__('Kelola Pengguna')">
    <div class="quest-page">

        {{-- Pesan berhasil --}}
        @if (session('success'))
            <div
                class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700 shadow-sm dark:border-emerald-900/70 dark:bg-emerald-950/50 dark:text-emerald-300"
            >
                <div
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900"
                >
                    <svg
                        class="size-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m5 12 4 4L19 6" />
                    </svg>
                </div>

                <div>
                    <p class="font-bold">Berhasil</p>
                    <p class="mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Pesan gagal --}}
        @if (session('error'))
            <div
                class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700 shadow-sm dark:border-red-900/70 dark:bg-red-950/50 dark:text-red-300"
            >
                <div
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900"
                >
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
                    <p class="font-bold">Tidak dapat diproses</p>
                    <p class="mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Hero --}}
        <section class="quest-hero">
            <div
                class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="max-w-3xl">
                    <div class="quest-eyebrow">
                        <span class="size-2 rounded-full bg-quest-500"></span>
                        ADMINISTRASI PENGGUNA
                    </div>

                    <h1
                        class="mt-5 text-3xl font-bold tracking-tight text-neutral-950 sm:text-4xl dark:text-white"
                    >
                        Kelola
                        <span
                            class="bg-gradient-to-r from-quest-500 to-quest-purple bg-clip-text text-transparent"
                        >
                            Pengguna Questify
                        </span>
                    </h1>

                    <p
                        class="mt-3 max-w-2xl text-sm leading-7 text-neutral-600 sm:text-base dark:text-neutral-300"
                    >
                        Tambahkan, perbarui, dan kelola akun Administrator,
                        Guru, serta Peserta Didik yang menggunakan platform
                        Questify.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a
                            href="{{ route('admin.users.create') }}"
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

                            Tambah Pengguna
                        </a>

                        <a
                            href="{{ route('admin.dashboard') }}"
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

                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>

                {{-- Dekorasi --}}
                <div class="relative hidden h-40 w-56 shrink-0 lg:block">
                    <div
                        class="absolute right-2 top-1 size-28 rotate-6 rounded-3xl bg-gradient-to-br from-blue-500 to-blue-900 shadow-2xl shadow-blue-500/30"
                    ></div>

                    <div
                        class="absolute bottom-0 left-2 flex size-28 -rotate-6 items-center justify-center rounded-3xl border border-white/30 bg-white/75 shadow-xl backdrop-blur dark:bg-neutral-900/75"
                    >
                        <svg
                            class="size-14 text-blue-600 dark:text-blue-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        {{-- Statistik --}}
        <section>
            <div class="mb-4">
                <h2
                    class="text-xl font-bold text-neutral-950 dark:text-white"
                >
                    Ringkasan Pengguna
                </h2>

                <p
                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                >
                    Distribusi akun berdasarkan role pengguna.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                {{-- Total --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                            >
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

                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                            >
                                Semua role
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Total Pengguna
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $totalUsers }}
                        </p>
                    </div>
                </article>

                {{-- Admin --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600 dark:bg-violet-950 dark:text-violet-300"
                            >
                                <svg
                                    class="size-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M12 3 4 7v5c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V7l-8-4Z" />
                                    <path d="M9 12l2 2 4-4" />
                                </svg>
                            </div>

                            <span
                                class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-600 dark:bg-violet-950 dark:text-violet-300"
                            >
                                Pengelola
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Administrator
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $totalAdmins }}
                        </p>
                    </div>
                </article>

                {{-- Guru --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"
                            >
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

                            <span
                                class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"
                            >
                                Pengajar
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Guru
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $totalTeachers }}
                        </p>
                    </div>
                </article>

                {{-- Peserta Didik --}}
                <article class="quest-stat-card">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div
                                class="flex size-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"
                            >
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

                            <span
                                class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Pembelajar
                            </span>
                        </div>

                        <p
                            class="mt-5 text-sm font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            Peserta Didik
                        </p>

                        <p
                            class="mt-1 text-4xl font-bold text-neutral-950 dark:text-white"
                        >
                            {{ $totalStudents }}
                        </p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Daftar pengguna --}}
        <section class="quest-card overflow-hidden">
            <div
                class="flex flex-col gap-4 border-b border-neutral-200 px-6 py-6 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-800"
            >
                <div>
                    <span class="quest-eyebrow">
                        DATABASE PENGGUNA
                    </span>

                    <h2
                        class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                    >
                        Daftar Pengguna
                    </h2>

                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Akun yang saat ini tersimpan pada database Questify.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="quest-badge">
                        {{ $totalUsers }} akun terdaftar
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead
                        class="border-b border-neutral-200 bg-neutral-50/80 text-xs uppercase tracking-wide text-neutral-500 dark:border-neutral-800 dark:bg-neutral-900/70 dark:text-neutral-400"
                    >
                        <tr>
                            <th class="px-6 py-4">No.</th>
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Terdaftar</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody
                        class="divide-y divide-neutral-200 dark:divide-neutral-800"
                    >
                        @forelse ($users as $user)
                            <tr
                                class="transition hover:bg-blue-50/60 dark:hover:bg-blue-950/20"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-neutral-500 dark:text-neutral-400"
                                >
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-900 text-xs font-bold text-white shadow-sm"
                                        >
                                            {{ $user->initials() }}
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="truncate font-bold text-neutral-950 dark:text-white"
                                            >
                                                {{ $user->name }}
                                            </p>

                                            @if (auth()->id() === $user->id)
                                                <p
                                                    class="mt-0.5 text-xs font-medium text-blue-600 dark:text-blue-300"
                                                >
                                                    Akun yang sedang digunakan
                                                </p>
                                            @else
                                                <p
                                                    class="mt-0.5 text-xs text-neutral-400"
                                                >
                                                    ID pengguna #{{ $user->id }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-neutral-600 dark:text-neutral-300"
                                >
                                    {{ $user->email }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($user->isAdmin())
                                        <span
                                            class="inline-flex rounded-full bg-violet-100 px-3 py-1.5 text-xs font-bold text-violet-700 dark:bg-violet-950 dark:text-violet-300"
                                        >
                                            Administrator
                                        </span>
                                    @elseif ($user->isTeacher())
                                        <span
                                            class="inline-flex rounded-full bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
                                        >
                                            Guru
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                                        >
                                            Peserta Didik
                                        </span>
                                    @endif
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-neutral-500 dark:text-neutral-400"
                                >
                                    {{ $user->created_at?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-neutral-300 bg-white px-3 py-2 text-xs font-bold text-neutral-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:border-blue-800 dark:hover:bg-blue-950/40 dark:hover:text-blue-300"
                                        >
                                            <svg
                                                class="size-3.5"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M12 20h9" />
                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                            </svg>

                                            Edit
                                        </a>

                                        @if (auth()->id() !== $user->id)
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus pengguna {{ addslashes($user->name) }}?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-red-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-red-700"
                                                >
                                                    <svg
                                                        class="size-3.5"
                                                        viewBox="0 0 24 24"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="2"
                                                    >
                                                        <path d="M3 6h18" />
                                                        <path d="M8 6V4h8v2" />
                                                        <path d="M19 6l-1 14H6L5 6" />
                                                        <path d="M10 11v5M14 11v5" />
                                                    </svg>

                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="inline-flex rounded-xl bg-blue-100 px-3 py-2 text-xs font-bold text-blue-700 dark:bg-blue-950 dark:text-blue-300"
                                            >
                                                Akun aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="px-6 py-16 text-center"
                                >
                                    <div
                                        class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-300"
                                    >
                                        <svg
                                            class="size-8"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        >
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                    </div>

                                    <h3
                                        class="mt-4 font-bold text-neutral-950 dark:text-white"
                                    >
                                        Belum terdapat pengguna
                                    </h3>

                                    <p
                                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                    >
                                        Tambahkan pengguna pertama untuk memulai.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Footer --}}
        <section
            class="flex flex-col gap-2 rounded-2xl border border-dashed border-neutral-300 px-5 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-700 dark:text-neutral-400"
        >
            <p>Questify — Learn by Doing. Think by Solving.</p>

            <span
                class="font-medium text-quest-600 dark:text-quest-300"
            >
                User Management
            </span>
        </section>
    </div>
</x-layouts::app>
