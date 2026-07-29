<x-layouts::app :title="'Koleksi Badge'">
    <div class="quest-page">
        {{-- Header --}}
        <section class="quest-card overflow-hidden">
            <div
                class="bg-gradient-to-r from-blue-50 via-white to-violet-50 p-6 dark:from-neutral-900 dark:via-neutral-900 dark:to-violet-950"
            >
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300"
                >
                    <span
                        class="h-2 w-2 rounded-full bg-violet-500"
                    ></span>
                    Penghargaan
                </span>

                <h1
                    class="mt-4 text-2xl font-bold text-neutral-950 dark:text-white"
                >
                    Koleksi Badge
                </h1>

                <p
                    class="mt-2 max-w-3xl text-sm leading-6 text-neutral-600 dark:text-neutral-300"
                >
                    Badge diberikan berdasarkan pencapaian selama
                    menyelesaikan aktivitas dan misi pembelajaran.
                </p>
            </div>
        </section>

        {{-- Ringkasan --}}
        <section class="grid gap-4 md:grid-cols-3">
            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-violet-600"
                >
                    Badge Dimiliki
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $ownedBadgeIds->count() }}
                    <span class="text-lg text-violet-600">
                        dari {{ $badges->count() }}
                    </span>
                </p>
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-amber-600"
                >
                    Total XP
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ number_format($totalXp, 0, ',', '.') }}
                    <span class="text-lg text-amber-600">XP</span>
                </p>
            </div>

            <div class="quest-card p-6">
                <p
                    class="text-xs font-semibold uppercase tracking-wider text-emerald-600"
                >
                    Misi Selesai
                </p>

                <p
                    class="mt-3 text-3xl font-bold text-neutral-950 dark:text-white"
                >
                    {{ $completedMissionCount }}
                </p>
            </div>
        </section>

        {{-- Koleksi seluruh badge --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-blue-600"
            >
                Daftar Penghargaan
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Badge Pembelajaran
            </h2>

            <p
                class="mt-2 text-sm leading-6 text-neutral-600 dark:text-neutral-300"
            >
                Badge berwarna telah diperoleh, sedangkan badge abu-abu
                masih terkunci.
            </p>

            <div class="mt-6 grid gap-5 md:grid-cols-3">
                @foreach ($badges as $badge)
                    @php
                        $isOwned = $ownedBadgeIds->contains($badge->id);

                        $themeClasses = match ($badge->theme) {
                            'amber' =>
                                'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-300',
                            'violet' =>
                                'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-800 dark:bg-violet-950 dark:text-violet-300',
                            default =>
                                'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-300',
                        };

                        $icon = match ($badge->icon) {
                            'flag' => '⚑',
                            'star' => '★',
                            'trophy' => '🏆',
                            default => '◆',
                        };
                    @endphp

                    <article
                        class="rounded-2xl border p-5 transition
                            {{ $isOwned
                                ? $themeClasses
                                : 'border-neutral-200 bg-neutral-100 text-neutral-400 grayscale dark:border-neutral-700 dark:bg-neutral-800' }}"
                    >
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-3xl shadow-sm dark:bg-neutral-900"
                        >
                            {{ $isOwned ? $icon : '🔒' }}
                        </div>

                        <p
                            class="mt-5 text-xs font-semibold uppercase tracking-wider"
                        >
                            {{ $isOwned ? 'Telah Diperoleh' : 'Terkunci' }}
                        </p>

                        <h3 class="mt-2 text-lg font-bold">
                            {{ $badge->name }}
                        </h3>

                        <p class="mt-2 text-sm leading-6">
                            {{ $badge->description }}
                        </p>

                        <div
                            class="mt-5 rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold dark:bg-neutral-900/60"
                        >
                            @switch($badge->criteria_type)
                                @case(\App\Models\Badge::CRITERIA_MISSIONS_COMPLETED)
                                    Selesaikan
                                    {{ $badge->criteria_value }} misi
                                    @break

                                @case(\App\Models\Badge::CRITERIA_TOTAL_XP)
                                    Kumpulkan
                                    {{ $badge->criteria_value }} XP
                                    @break

                                @case(\App\Models\Badge::CRITERIA_PERFECT_CHALLENGE)
                                    Raih nilai
                                    {{ $badge->criteria_value }}
                                    pada Final Challenge
                                    @break
                            @endswitch
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Riwayat perolehan --}}
        <section class="quest-card p-6">
            <p
                class="text-xs font-semibold uppercase tracking-wider text-violet-600"
            >
                Riwayat Pencapaian
            </p>

            <h2
                class="mt-2 text-xl font-bold text-neutral-950 dark:text-white"
            >
                Badge yang Telah Diperoleh
            </h2>

            @if ($userBadges->isEmpty())
                <div
                    class="mt-5 rounded-2xl border border-dashed border-neutral-300 p-8 text-center dark:border-neutral-700"
                >
                    <p
                        class="font-semibold text-neutral-900 dark:text-white"
                    >
                        Belum ada badge yang diperoleh
                    </p>

                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Selesaikan misi dan kumpulkan XP untuk membuka
                        badge pertamamu.
                    </p>
                </div>
            @else
                <div class="mt-5 space-y-4">
                    @foreach ($userBadges as $userBadge)
                        <article
                            class="flex flex-col gap-4 rounded-2xl border border-neutral-200 p-5 dark:border-neutral-700 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <h3
                                    class="font-bold text-neutral-950 dark:text-white"
                                >
                                    {{ $userBadge->badge->name }}
                                </h3>

                                <p
                                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                                >
                                    Diperoleh pada
                                    {{ $userBadge->awarded_at
                                        ->locale('id')
                                        ->translatedFormat(
                                            'd F Y, H:i'
                                        ) }}
                                </p>
                            </div>

                            <span
                                class="w-fit rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300"
                            >
                                Badge Terbuka
                            </span>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
