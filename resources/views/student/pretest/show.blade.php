<x-layouts::app :title="'Pretest - ' . $mission->title">
    <div class="quest-page">
        <section class="quest-hero">
            <div class="relative z-10">
                <div class="quest-eyebrow">
                    <span class="size-2 rounded-full bg-blue-500"></span>
                    Pretest Misi {{ $mission->sequence }}
                </div>

                <h1 class="mt-5 text-3xl font-bold text-neutral-950 dark:text-white">
                    Pretest: {{ $mission->title }}
                </h1>

                <p class="mt-3 max-w-3xl text-neutral-600 dark:text-neutral-300">
                    Pretest digunakan untuk mengetahui pemahaman awalmu sebelum
                    mempelajari materi pada misi ini.
                </p>

                <div class="mt-6">
                    <a
                        href="{{ route('student.missions.show', $mission) }}"
                        class="quest-button-primary"
                        wire:navigate
                    >
                        Kembali ke Detail Misi
                    </a>
                </div>
            </div>
        </section>

        <section class="quest-card p-6">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">
                        Informasi Pretest
                    </p>

                    <h2 class="mt-2 text-xl font-bold text-neutral-950 dark:text-white">
                        Persiapan Sebelum Memulai
                    </h2>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-300">
                        Jawablah setiap soal berdasarkan kemampuanmu sendiri.
                        Hasil Pretest tidak langsung menampilkan kunci jawaban.
                    </p>
                </div>

                <div class="rounded-2xl bg-blue-50 px-5 py-4 dark:bg-blue-950/40">
                    <p class="text-sm text-neutral-600 dark:text-neutral-300">
                        Jumlah Soal
                    </p>

                    <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-300">
                        {{ $questionCount }} soal
                    </p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700">
                    <p class="text-sm font-semibold text-neutral-950 dark:text-white">
                        Satu Kali Pengerjaan
                    </p>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Pretest hanya dapat diselesaikan satu kali pada misi ini.
                    </p>
                </article>

                <article class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700">
                    <p class="text-sm font-semibold text-neutral-950 dark:text-white">
                        Jawab Semua Soal
                    </p>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Pastikan tidak ada soal yang terlewat sebelum mengirim jawaban.
                    </p>
                </article>

                <article class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700">
                    <p class="text-sm font-semibold text-neutral-950 dark:text-white">
                        Kerjakan Secara Mandiri
                    </p>

                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        Jawablah sesuai pemahaman awal tanpa membuka materi pembelajaran.
                    </p>
                </article>
            </div>

            <div class="mt-6 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                @if ($attempt?->status === 'completed')
                    <div class="rounded-2xl bg-emerald-50 p-5 dark:bg-emerald-950/30">
                        <p class="font-semibold text-emerald-700 dark:text-emerald-300">
                            Pretest telah selesai
                        </p>

                        <p class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">
                            Kamu telah menyelesaikan Pretest pada misi ini.
                        </p>
                    </div>
                @elseif ($attempt?->status === 'in_progress')
                    <div class="rounded-2xl bg-amber-50 p-5 dark:bg-amber-950/30">
                        <p class="font-semibold text-amber-700 dark:text-amber-300">
                            Pengerjaan belum selesai
                        </p>

                        <p class="mt-1 text-sm text-amber-600 dark:text-amber-400">
                            Kamu memiliki pengerjaan Pretest yang masih berlangsung.
                        </p>
                    </div>
                    <a
    href="{{ route('student.missions.pretest.questions', [
        'mission' => $mission->slug,
    ]) }}"
    class="quest-button-primary mt-5 inline-flex"
    wire:navigate
>
    Lanjutkan Pretest
</a>
                @else
    <form
        method="POST"
        action="{{ route('student.missions.pretest.start', ['mission' => $mission->slug]) }}"
    >
        @csrf

        <button
            type="submit"
            class="quest-button-primary"
        >
            Mulai Pretest
        </button>
    </form>

    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">
        Waktu pengerjaan akan mulai dicatat setelah tombol ditekan.
    </p>
@endif
            </div>
        </section>
    </div>
</x-layouts::app>
