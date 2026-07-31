<x-layouts::app :title="'Posttest - ' . $mission->title">
    <div class="quest-page">
        <section class="quest-hero">
            <div class="relative z-10">
                <div class="quest-eyebrow">
                    <span class="size-2 rounded-full bg-emerald-500"></span>
                    Posttest Misi {{ $mission->sequence }}
                </div>

                <h1 class="mt-5 text-3xl font-bold text-neutral-950 dark:text-white">
                    Posttest: {{ $mission->title }}
                </h1>

                <p class="mt-3 max-w-3xl text-neutral-600 dark:text-neutral-300">
                    Posttest digunakan untuk mengukur pemahamanmu setelah
                    menyelesaikan seluruh rangkaian pembelajaran pada misi ini.
                </p>

                <div class="mt-6">
                    <a
                        href="{{ route('student.missions.show', [
                            'mission' => $mission->slug,
                        ]) }}"
                        class="quest-button-primary"
                        wire:navigate
                    >
                        Kembali ke Detail Misi
                    </a>
                </div>
            </div>
        </section>

        <section class="quest-card p-6">
            <div
                class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400"
                    >
                        Informasi Posttest
                    </p>

                    <h2
                        class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
                    >
                        Persiapan Sebelum Memulai
                    </h2>

                    <p
                        class="mt-2 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                    >
                        Jawablah setiap soal berdasarkan pemahaman yang telah
                        kamu peroleh setelah mengikuti seluruh kegiatan
                        pembelajaran pada misi ini.
                    </p>
                </div>

                <div
                    class="rounded-2xl bg-emerald-50 px-5 py-4 dark:bg-emerald-950/40"
                >
                    <p class="text-sm text-neutral-600 dark:text-neutral-300">
                        Jumlah Soal
                    </p>

                    <p
                        class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300"
                    >
                        {{ $questionCount }} soal
                    </p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <article
                    class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-950 dark:text-white"
                    >
                        Satu Kali Pengerjaan
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Posttest hanya dapat diselesaikan satu kali pada misi ini.
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-950 dark:text-white"
                    >
                        Jawab Semua Soal
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Pastikan tidak ada soal yang terlewat sebelum mengirim
                        jawaban.
                    </p>
                </article>

                <article
                    class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700"
                >
                    <p
                        class="text-sm font-semibold text-neutral-950 dark:text-white"
                    >
                        Kerjakan Secara Mandiri
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Jawablah berdasarkan pemahamanmu setelah menyelesaikan
                        pembelajaran.
                    </p>
                </article>
            </div>

            <div
                class="mt-6 border-t border-neutral-200 pt-6 dark:border-neutral-700"
            >
                @if ($attempt?->status === 'completed')
                    <div
                        class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-800 dark:bg-emerald-950/30"
                    >
                        <p
                            class="font-semibold text-emerald-700 dark:text-emerald-300"
                        >
                            Posttest telah selesai
                        </p>

                        <p
                            class="mt-1 text-sm text-emerald-600 dark:text-emerald-400"
                        >
                            Jawabanmu telah tersimpan dan Posttest pada misi ini
                            telah berhasil diselesaikan.
                        </p>

                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <div
                                class="rounded-xl bg-white p-4 dark:bg-neutral-900"
                            >
                                <p
                                    class="text-sm text-neutral-500 dark:text-neutral-400"
                                >
                                    Nilai Posttest
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300"
                                >
                                    {{ number_format((float) $attempt->score, 2) }}
                                    <span class="text-base">/100</span>
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-white p-4 dark:bg-neutral-900"
                            >
                                <p
                                    class="text-sm text-neutral-500 dark:text-neutral-400"
                                >
                                    Jawaban Benar
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold text-neutral-950 dark:text-white"
                                >
                                    {{ $attempt->correct_answers }}
                                    dari
                                    {{ $attempt->total_questions }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif ($attempt?->status === 'in_progress')
                    <div
                        class="rounded-2xl bg-amber-50 p-5 dark:bg-amber-950/30"
                    >
                        <p
                            class="font-semibold text-amber-700 dark:text-amber-300"
                        >
                            Pengerjaan belum selesai
                        </p>

                        <p
                            class="mt-1 text-sm text-amber-600 dark:text-amber-400"
                        >
                            Kamu memiliki pengerjaan Posttest yang masih
                            berlangsung.
                        </p>
                    </div>

                    <a
                        href="{{ route('student.missions.posttest.questions', [
                            'mission' => $mission->slug,
                        ]) }}"
                        class="quest-button-primary mt-5 inline-flex"
                        wire:navigate
                    >
                        Lanjutkan Posttest
                    </a>
                @else
                    <form
                        method="POST"
                        action="{{ route('student.missions.posttest.start', [
                            'mission' => $mission->slug,
                        ]) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="quest-button-primary"
                        >
                            Mulai Posttest
                        </button>
                    </form>

                    <p
                        class="mt-3 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Waktu pengerjaan akan mulai dicatat setelah tombol
                        ditekan.
                    </p>
                @endif
            </div>
        </section>
    </div>
</x-layouts::app>
