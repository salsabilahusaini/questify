<x-layouts::app :title="$mission->title">
    <div class="quest-page">
        <section class="quest-hero">
            <div class="relative z-10">
                <div class="quest-eyebrow">
                    <span class="size-2 rounded-full bg-violet-500"></span>
                    Misi {{ $mission->sequence }}
                </div>

                <h1 class="mt-5 text-3xl font-bold text-neutral-950 dark:text-white">
                    {{ $mission->title }}
                </h1>

                <p class="mt-3 max-w-3xl text-neutral-600 dark:text-neutral-300">
                    {{ $mission->description }}
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a
                        href="{{ route('student.missions.index') }}"
                        class="quest-button-primary"
                        wire:navigate
                    >
                        Kembali ke Daftar Misi
                    </a>

                    <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-700">
                        Reward {{ $mission->xp_reward }} XP
                    </span>
                </div>
            </div>
        </section>

        <section class="quest-card p-6">
            <h2 class="text-2xl font-bold text-neutral-950 dark:text-white">
                Alur Kegiatan
            </h2>

            <div class="mt-5 space-y-4">
                @foreach ($steps as $step)
                    <article class="rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700">
                        <div class="flex items-start gap-4">
                            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 font-bold text-white">
                                {{ $step['number'] }}
                            </div>

                            <div>
                                <h3 class="font-bold text-neutral-950 dark:text-white">
                                    {{ $step['title'] }}
                                </h3>

                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $step['description'] }}
                                </p>

                                <p class="mt-3 text-xs font-semibold uppercase">
    {{ match ($step['status']) {
        'completed' => 'S elesai',
        'available' => 'Siap dimulai',
        default => 'Terkunci',
    } }}
</p>

@if ($step['status'] === 'available' && ! empty($step['url']))
    <a
        href="{{ $step['url'] }}"
        class="quest-button-primary mt-4 inline-flex"
        wire:navigate
    >
        Buka {{ $step['title'] }}
    </a>
@endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts::app>
