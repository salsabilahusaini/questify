<x-layouts::app :title="__('Kelola Pengguna')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    @if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
        {{ session('error') }}
    </div>
@endif

        {{-- Judul halaman --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                Administrasi Pengguna
            </p>

            <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">
                Kelola Pengguna
            </h1>

            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Lihat dan kelola akun Admin, Guru, serta Peserta Didik yang
                terdaftar pada platform Questify.
            </p>
        </section>

        {{-- Ringkasan pengguna --}}
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Total Pengguna
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalUsers }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Admin
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalAdmins }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Guru
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalTeachers }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Peserta Didik
                </p>

                <p class="mt-2 text-3xl font-bold text-neutral-900 dark:text-white">
                    {{ $totalStudents }}
                </p>
            </div>
        </section>

        {{-- Tabel pengguna --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
            Daftar Pengguna
        </h2>

        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Data akun yang saat ini tersimpan di database Questify.
        </p>
    </div>

    <a
        href="{{ route('admin.users.create') }}"
        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
    >
        Tambah Pengguna
    </a>
</div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 text-xs uppercase text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-4 py-3">No.</th>
                              <th class="px-4 py-3">Nama</th>
                              <th class="px-4 py-3">Email</th>
                              <th class="px-4 py-3">Role</th>
                              <th class="px-4 py-3">Terdaftar</th>
                              <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($users as $user)
                            <tr class="text-neutral-700 dark:text-neutral-300">
                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 font-medium text-neutral-900 dark:text-white">
                                    {{ $user->name }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $user->email }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    @if ($user->role === 'admin')
                                        <span class="inline-flex rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                            Admin
                                        </span>
                                    @elseif ($user->role === 'teacher')
                                        <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                            Guru
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                            Peserta Didik
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $user->created_at?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4">
    <div class="flex items-center justify-end gap-2">
        <a
            href="{{ route('admin.users.edit', $user) }}"
            class="inline-flex rounded-lg border border-neutral-300 px-3 py-2 text-xs font-semibold text-neutral-700 transition hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
        >
            Edit
        </a>

        @if (auth()->id() !== $user->id)
            <form
                method="POST"
                action="{{ route('admin.users.destroy', $user) }}"
                onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="inline-flex rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700"
                >
                    Hapus
                </button>
            </form>
        @else
            <span class="rounded-lg bg-neutral-100 px-3 py-2 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
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
                                    class="px-4 py-10 text-center text-neutral-500 dark:text-neutral-400"
                                >
                                    Belum terdapat pengguna yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </section>

    </div>
</x-layouts::app>
